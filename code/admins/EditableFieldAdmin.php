<?php

/**
 * EditableFieldAdmin is an admin class for managing the editable fields in the system.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 * @package editablefield
 * @property int    $ID
 * @property string $Name
 */
class EditableFieldAdmin extends LeftAndMain {
	private static $url_segment = 'editablefield';
	private static $menu_title = 'Editable fields';
	private static $tree_class = 'EditableField';
	private static $allowed_actions = [
		'SearchForm',
		'filter',
		'doAdd',
		'delete',
		'addOptionField'
	];
	private static $menu_icon = 'editablefield/images/icon.png';

	/**
	 * List of current displayed fields
	 *
	 * @var \DataList
	 */
	protected $formFields;

	/**
	 * (non-PHPdoc)
	 *
	 * @see LeftAndMain::init()
	 */
	public function init() {
		parent::init();
		Requirements::css('editablefield/css/EditableField.css');
		Requirements::javascript('editablefield/javascript/EditableField.js');
	}

	/**
	 * The content of the main section of the page
	 * List current form fields and allow user to modify their data
	 *
	 * @see LeftAndMain::getEditForm()
	 */
	public function getEditForm($id = null, $fields = null) {
		// Save button
		$actions = new FieldList(
			FormAction::create('save', _t('CMSMain.SAVE', 'Save'))
				->setUseButtonTag(true)
				->addExtraClass('ss-ui-action-constructive')
				->setAttribute('data-icon', 'accept')
		);

		// Form tabs
		$fieldsTab = new Tab('Fields', _t('EditableFieldAdmin.Fields', 'Fields'));
		$groupsTab = new Tab('Groups', singleton('Group')->i18n_plural_name());

		// Form tab container
		$tabSet = new TabSet('Root', $fieldsTab, $groupsTab);
		$tabSet->setTemplate('CMSTabSet');

		// Activate tab based on request param
		$actionParam = $this->request->param('Action');
		if ($actionParam == 'fields') {
			$fieldsTab->addExtraClass('ui-state-active');
		} elseif ($actionParam == 'groups') {
			$groupsTab->addExtraClass('ui-state-active');
		}

		// Form field list
		$fields = new FieldList([$tabSet]);

		// Add field to first tab
		$fields->addFieldToTab('Root.Fields',
		                       new EditableFieldEditor("Fields", 'Fields', "")
		);

		// Add field to second tab
		$groupsConfig = GridFieldConfig_RecordEditor::create();
		$groupsField = GridField::create(
			'EditableFieldGroup',
			singleton('Group')->i18n_plural_name(),
			EditableFieldGroup::get(),
			$groupsConfig
		);
		$component = $groupsConfig->getComponentByType('GridFieldAddNewButton');
		$component->setButtonName(_t('EditableFieldAdmin.AddGroup', 'Add Group'));
		$fields->addFieldToTab('Root.Groups', $groupsField);

		// The edit form
		$form = CMSForm::create($this, 'EditForm', $fields, $actions)->setHTMLID('Form_EditForm');
		$form->loadDataFrom($this);

		// Render correct responses on validation errors
		$form->setResponseNegotiator($this->getResponseNegotiator());
		$form->disableDefaultAction();

		// Form layout
		$form->addExtraClass('cms-content cms-edit-form center ss-tabset cms-tabset ' . $this->BaseCSSClasses());
		$form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));
		$form->setAttribute('data-pjax-fragment', 'CurrentForm');

		// Allow decorators to modify the form
		$this->extend('updateEditForm', $form);

		return $form;
	}

	/**
	 * Action to add a field to the field editor. Called via an ajax get
	 *
	 * @param SS_HTTPRequest $request
	 *
	 * @throws SS_HTTPResponse_Exception
	 * @throws ValidationException
	 * @return bool|string|HTMLText
	 */
	public function doAdd($request) {
		// Check security token
		if (!SecurityToken::inst()->checkRequest($request)) {
			return $this->httpError(400);
		}

		// Can user create a feild
		if (!$this->canCreate()) {
			return Security::permissionFailure();
		}

		// EditableField type is required
		$className = $this->request->postVar('Type');
		if (!$className) {
			throw new ValidationException(_t('EditableFieldAdmin.MISSINGFIELDTYPE',
			                                 'Please select a field type to created'));
		}

		// Class name must be a subclass of EditableField
		// Then instantiate the class, create new data, and render display row
		if (is_subclass_of($className, "EditableField")) {
			/** @var EditableField $field */
			$field = Object::create($className);

			$newID = $this->request->postVar('NewID');
			if ($newID > 0) {
				$newID = $field->ID;
			}
			$field->Name = $field->class . $newID;
			$field->write();

			return $field->EditSegment();
		}

		throw new ValidationException(_t('EditableFieldAdmin.INVALIDFIELDTYPE', 'Invalid field type selected'));
	}

	/**
	 * Action to filter the list of fields in field editor.
	 *
	 * @return SS_HTTPResponse
	 */
	public function filter() {
		return $this->getResponseNegotiator()->respond($this->request);
	}

	/**
	 * Action for saving the form fields listed in field editor
	 *
	 * @param array   $record
	 * @param CMSForm $form
	 *
	 * @return SS_HTTPResponse
	 */
	public function save($record, $form) {
		// Can user edit fields details
		if (!$this->canEdit()) {
			return Security::permissionFailure();
		}

		// Update submitted fields details
		if (!empty($record['Fields']) && is_array($record['Fields'])) {
			foreach ($record['Fields'] as $newEditableID => $newEditableData) {
				// Field ID is the index of the item
				if (!is_numeric($newEditableID)) {
					continue;
				}

				// Get it from the db
				$editable = DataObject::get_by_id('EditableField', $newEditableID);

				// If it exists in the db update it
				if ($editable) {
					$editable->populateFromPostData($newEditableData);
				}
			}
		}

		// Allow decorators to execute after save
		$this->extend('onAfterSave', $record);

		// Response message
		$this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.SAVEDUP', 'Saved.')));

		return $this->getResponseNegotiator()->respond($this->request);
	}

	/**
	 * Action to delete form field
	 *
	 * @param array   $record
	 * @param CMSForm $form
	 *
	 * @return SS_HTTPResponse
	 */
	public function delete($record, $form) {
		// Can user delete the field
		if (!$this->canDelete()) {
			return Security::permissionFailure();
		}

		// Field ID
		$fieldID = (int)key($record['Fields']);

		// Get it from the db
		$editable = DataObject::get_by_id('EditableField', $fieldID);

		// If it exists in the db delete it
		if (!$editable) {
			return $this->httpError(404, _t('EditableFieldAdmin.INVALIDFIELDTYPE', 'Invalid field type selected'));
		}
		$editable->delete();

		// Allow decorators to execute after save
		$this->extend('onAfterDelete', $record);

		// Response message
		$this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.DELETED', 'Deleted.')));

		return $this->getResponseNegotiator()->respond($this->request);
	}

	/**
	 * Action to create field option (ie. dropdown, checkbox). Called from ajax request.
	 *
	 * @param $record
	 *
	 * @return bool|SS_HTTPResponse|void
	 * @throws SS_HTTPResponse_Exception
	 */
	public function addOptionField($record) {
		// Check security token
		if (!SecurityToken::inst()->checkRequest($this->request)) {
			return $this->httpError(400);
		}

		// Can user edit fields details
		if (!$this->canEdit()) {
			return Security::permissionFailure();
		}

		// The parent id of the option field (the id of the field (ie. dropdown)
		$parent = (isset($record['Parent'])) ? $record['Parent'] : false;

		if ($parent) {
			$parentObj = EditableField::get()->byID($parent);
			$optionClass = ($parentObj && $parentObj->exists()) ? $parentObj->getRelationClass('Options') : 'EditableFieldOption';

			// Work out the sort by getting the sort of the last field in the form +1
			$sqlQuery = new SQLQuery();
			$sqlQuery = $sqlQuery
				->setSelect("MAX(\"Sort\")")
				->setFrom("\"EditableFieldOption\"")
				->setWhere("\"ParentID\" = " . (int)$parent);

			$sort = $sqlQuery->execute()->value() + 1;

			if ($parent) {
				$object = Injector::inst()->create($optionClass);
				$object->write();
				$object->ParentID = $parent;
				$object->Sort = $sort;
				$object->Name = 'option' . $object->ID;
				$object->write();

				return $object->EditSegment();
			}
		}

		return false;
	}

	/**
	 * Returns a Form for field searching for use in templates.
	 * Can be modified from a decorator by a 'updateSearchForm' method
	 *
	 * @return CMSForm
	 */
	public function SearchForm() {
		// Search fields
		$fields = new FieldList(
			new TextField('q[Term]', _t('CMSSearch.QUERY', 'Query'))
		);

		// Submit and reset buttons
		$actions = new FieldList(
			FormAction::create('doSearch', _t('CMSMain_left_ss.APPLY_FILTER', 'Apply Filter'))
				->addExtraClass('ss-ui-action-constructive'),
			Object::create('ResetFormAction', 'clear', _t('CMSMain_left_ss.RESET', 'Reset'))
		);

		// Use <button> to allow full jQuery UI styling on the all of the Actions
		foreach ($actions->dataFields() as $action) {
			$action->setUseButtonTag(true);
		}

		// Create the form
		$form = CMSForm::create($this, 'SearchForm', $fields, $actions)
			->addExtraClass('cms-search-form')
			->setFormMethod('GET')
			->setFormAction($this->Link('filter'))
			->disableSecurityToken()
			->unsetValidator();
		$form->setResponseNegotiator($this->getResponseNegotiator());
		$form->disableDefaultAction();

		// Load the form with previously sent search data
		$form->loadDataFrom($this->request->getVars());

		// Allow decorators to modify the form
		$this->extend('updateSearchForm', $form);

		return $form;
	}

	/**
	 * Returns a Form for adding a new field for use in templates.
	 * Can be modified from a decorator by a 'updateAddNewForm' method
	 *
	 * @return CMSForm
	 */
	public function AddForm() {
		// List of creatable form fields
		$typeDropdown = new DropdownField(
			'Type', _t('EditableFieldAdmin.SELECTAFIELD', 'Select a Field'), $this->getCreatableFields()
		);
		$typeDropdown->setEmptyString(' ');

		// Form fields
		$fields = new FieldList(
			$typeDropdown
		);

		// Form add button
		$actions = new FieldList(
			FormAction::create('doAddField', _t('EditableField.ADD', 'Add'))
				->addExtraClass('ss-ui-action-constructive')
				->setAttribute('data-icon', 'add')
				->setUseButtonTag(true)
		);

		// Form validators
		$validator = new RequiredFields();

		// Create the form
		$form = CMSForm::create($this, 'AddNewForm', $fields, $actions, $validator)
			->addExtraClass('cms-search-form')
			->setFormMethod('POST')
			->setFormAction($this->Link('doAdd'));

		// Allow decorators to modify the form
		$this->extend('updateAddNewForm', $form);

		return $form;
	}

	/**
	 * Return a {@link ArrayList} of all the addable fields to populate the add
	 * field menu.
	 *
	 * @return array
	 */
	public function getCreatableFields() {
		$fields = ClassInfo::subclassesFor('EditableField');
		$output = [];

		if (!empty($fields)) {
			array_shift($fields); // get rid of subclass 0
			asort($fields); // get in order

			foreach ($fields as $field => $title) {
				// Skip an abstract class
				if ($field == "EditableFieldMultipleOption") {
					continue;
				}

				// Get the nice title and strip out field
				$niceTitle = _t($field . '.SINGULARNAME', $title);
				if ($niceTitle) {
					$output[$field] = $niceTitle;
				}
			}
		}

		return $output;
	}

	/**
	 * The data source of the field editor.
	 *
	 * @return DataList
	 */
	public function EditableFieldEditor() {
		if (null === $this->formFields) {
			$this->formFields = DataObject::get('EditableField')->limit(50)->sort('Title', 'ASC');
		}

		$query = $this->request->requestVar('q');
		if (!empty($query['Term'])) {
			$filters = array_fill_keys(['Name:PartialMatch', 'Title:PartialMatch'], $query['Term']);
			$this->formFields = $this->formFields->filterAny($filters)->sort('Title', 'ASC');
		}

		return $this->formFields;
	}

	/**
	 * Whether or not the user can edit the data in this object
	 *
	 * @see LeftAndMain::canView()
	 */
	public function canView($member = null) {
		return (boolean)Permission::check('CMS_ACCESS_EditableFieldAdmin', 'any', $member);
	}

	/**
	 * Whether or not the user can edit the data in this object
	 *
	 * @param Member $member
	 *
	 * @return boolean
	 */
	public function canEdit($member = null) {
		return (boolean)Permission::check('CMS_ACCESS_EditableFieldAdmin', 'any', $member);
	}

	/**
	 * Whether or not the user can delete the data in this object
	 *
	 * @param Member $member
	 *
	 * @return boolean
	 */
	public function canDelete($member = null) {
		return (boolean)Permission::check('CMS_ACCESS_EditableFieldAdmin', 'any', $member);
	}

	/**
	 * Whether or not the user can create data in this object
	 *
	 * @param Member $member
	 *
	 * @return boolean
	 */
	public function canCreate($member = null) {
		return (boolean)Permission::check('CMS_ACCESS_EditableFieldAdmin', 'any', $member);
	}

}

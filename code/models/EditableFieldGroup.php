<?php

/**
 * EditableFieldGroup is a data object class for editable field group
 *
 * @package editablefield
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 * @method ManyManyList Fields()
 */
class EditableFieldGroup extends DataObject {
	private static $db = [
		"Title" => "Varchar(255)",
	];

	private static $many_many = [
		'Fields' => 'EditableField'
	];

	private static $many_many_extraFields = [
		'Fields' => [
			"Sort" => "Int",
		]
	];

	private static $default_sort = '"Title"';

	private static $singular_name = 'Group';

	private static $plural_name = 'Groups';

	/**
	 * Make sure the title is required field
	 *
	 * @return RequiredFields
	 */
	public function getCMSValidator() {
		return new RequiredFields('Title');
	}

	/**
	 * Returns form fields for adding/editing the data object
	 *
	 * @return FieldList
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();

		// Field visible on edit only
		if ($this->isInDB()) {
			$config = GridFieldConfig_RelationEditor::create();
			$config
				->getComponentByType('GridFieldPaginator')
				->setItemsPerPage(10);
			$config->removeComponentsByType('GridFieldAddNewButton');
			$config->removeComponentsByType('GridFieldEditButton');
			$config
				->getComponentByType('GridFieldDataColumns')
				->setDisplayFields([
					                   'Name'  => _t('ConfigurablePage.NAME', 'Name'),
					                   'Title' => _t('ConfigurablePage.TITLE', 'Title'),
					                   'Sort'  => _t('ConfigurablePage.SORT', 'Sort'),
				                   ]);
			$config->addComponent(
				new GridFieldEditableManyManyExtraColumns(
					['Sort' => 'Int']
				),
				'GridFieldEditButton'
			);
			$field = new GridField('Fields', 'Fields', $this->Fields(), $config);
			$fields->addFieldToTab('Root.Fields', $field);

			// Remove Configurable Page Tab
			if (!$this->ConfigurablePages) {
				$fields->removeByName('ConfigurablePages');
			}
		}

		return $fields;
	}
}

<?php

/**
 * EditableFieldPageTypeList is an object representing page types dropdown field created by CMS admin
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 * @package editablefield
 */
class EditableFieldPageTypeList extends EditableField {
	private static $singular_name = 'Page Type List Field';
	private static $plural_name = 'Page Type List Fields';

	public function getFieldConfiguration() {
		$pageType = ($this->getSetting('PageTypeName')) ? $this->getSetting('PageTypeName') : 0;
		// Get all subclasses of Page class
		$allTypes = ClassInfo::subclassesFor('Page');

		// Create sorted array with keys and values are class names
		$types = [];
		foreach ($allTypes as $type) {
			$types[(string)$type] = Page::create_from_string($type)->singular_name();
		}
		asort($types);

		$fields = new FieldList(
			new DropdownField("Fields[$this->ID][CustomSettings][PageTypeName]",
			                  _t('EditableFieldPageTypeList.PAGETYPENAME', 'Page Type Name'), $types, $pageType)
		);

		return $fields;
	}

	protected function initFormField() {
		if ($this->getSetting('PageTypeName')) {
			$pages = Page::get($this->getSetting('PageTypeName'))->map('ID', 'Title');

			return new DropdownField($this->Name, $this->Title, $pages);
		}

		return false;
	}

	public function getIcon() {
		return 'editablefield/images/formfields/editablefielddropdown.png';
	}

}

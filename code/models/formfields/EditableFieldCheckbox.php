<?php

/**
 * EditableFieldCheckbox is an object representing a checkbox created by CMS admin
 *
 * @package editablefield
 * @author silverstripe/userforms
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class EditableFieldCheckbox extends EditableField {
	private static $singular_name = 'Checkbox Field';
	private static $plural_name = 'Checkboxes';

	public function getFieldConfiguration() {
		$options = parent::getFieldConfiguration();
		$options->push(new CheckboxField("Fields[$this->ID][CustomSettings][Default]", _t('EditableField.CHECKEDBYDEFAULT', 'Checked by Default?'), $this->getSetting('Default')));

		return $options;
	}

	protected function initFormField() {
		return new CheckboxField($this->Name, $this->Title, $this->getSetting('Default'));
	}

}

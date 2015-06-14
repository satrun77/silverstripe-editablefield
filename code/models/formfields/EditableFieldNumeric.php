<?php

/**
 * EditableFieldNumeric is an object representing numeric field created by CMS admin
 *
 * @package editablefield
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class EditableFieldNumeric extends EditableField {
	private static $singular_name = 'Numeric Field';
	private static $plural_name = 'Numeric Fields';

	protected function initFormField() {
		$field = new NumericField($this->Name, $this->Title);
		$field->addExtraClass('number');

		return $field;
	}

	public function getFieldValidationOptions() {
		$fields = parent::getFieldValidationOptions();

		$min = ($this->getSetting('MinValue')) ? $this->getSetting('MinValue') : '';
		$max = ($this->getSetting('MaxValue')) ? $this->getSetting('MaxValue') : '';

		$extraFields = new FieldList(
			new NumericField($this->getSettingName('MinValue'), _t('EditableField.MINVALUE', 'Min Value'), $min),
			new NumericField($this->getSettingName('MaxValue'), _t('EditableField.MAXVALUE', 'Max Value'), $max)
		);

		$fields->merge($extraFields);

		return $fields;
	}

}

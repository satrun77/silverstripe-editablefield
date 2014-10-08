<?php

/**
 * EditableFieldDate is an object representing date field created by CMS admin
 * 
 * @package editablefield
 * @author silverstripe/userforms
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class EditableFieldDate extends EditableField {
	private static $singular_name = 'Date Field';
	private static $plural_name = 'Date Fields';

	public function getFieldConfiguration() {
		$default = ($this->getSetting('DefaultToToday')) ? $this->getSetting('DefaultToToday') : false;
		$label = _t('EditableField.DEFAULTTOTODAY', 'Default to Today?');

		return new FieldList(
			new CheckboxField($this->getSettingName("DefaultToToday"), $label, $default)
		);
	}

	public function populateFromPostData($data) {
		$fieldPrefix = 'Default-';

		if(empty($data['Default']) && !empty($data[$fieldPrefix . 'Year']) && !empty($data[$fieldPrefix . 'Month']) && !empty($data[$fieldPrefix . 'Day'])) {
			$data['Default'] = $data['Year'] . '-' . $data['Month'] . '-' . $data['Day'];
		}

		parent::populateFromPostData($data);
	}

	protected function initFormField() {
		$defaultValue = ($this->getSetting('DefaultToToday')) ? date('Y-m-d') : $this->Default;
		$field = new DateField($this->Name, $this->Title, $defaultValue);
		$field->setConfig('showcalendar', true);

		return $field;
	}

}

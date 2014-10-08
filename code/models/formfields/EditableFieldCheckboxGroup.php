<?php

/**
 * EditableFieldCheckboxGroup is an object representing a set of selectable radio buttons created by CMS admin
 *
 * @package editablefield
 * @author silverstripe/userforms
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class EditableFieldCheckboxGroup extends EditableFieldMultipleOption {
	private static $singular_name = "Checkbox Group";
	private static $plural_name = "Checkbox Groups";

	protected function initFormField() {
		$optionSet = $this->Options();
		$optionMap = ($optionSet) ? $optionSet->map('EscapedTitle', 'Title') : array();

		return new CheckboxSetField($this->Name, $this->Title, $optionMap);
	}

}

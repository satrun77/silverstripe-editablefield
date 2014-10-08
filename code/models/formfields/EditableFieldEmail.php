<?php

/**
 * EditableFieldEmail is an object representing email field created by CMS admin
 *
 * @package editablefield
 * @author silverstripe/userforms
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class EditableFieldEmail extends EditableField {
	private static $singular_name = 'Email Field';
	private static $plural_name = 'Email Fields';

	protected function initFormField() {
		return new EmailField($this->Name, $this->Title);
	}

}

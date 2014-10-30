<?php

/**
 * EditableFieldEditor is a form field that lists editable fields and allows to modify their data
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 * @package editablefield
 */
class EditableFieldEditor extends FormField {
	/**
	 * List of current managed editable fields
	 *
	 * @var \DataList
	 */
	protected $fields;

	/**
	 * (non-PHPdoc)
	 * @see FormField::FieldHolder()
	 */
	public function FieldHolder($properties = array()) {
		return $this->renderWith("EditableFieldEditor", $properties);
	}

	/**
	 * Return the fields managed by the form field
	 *
	 * @return DataList
	 */
	public function Fields() {
		if(null === $this->fields) {
			$this->fields = $this->form->getRecord()->EditableFieldEditor();
		}
		return $this->fields;
	}

}

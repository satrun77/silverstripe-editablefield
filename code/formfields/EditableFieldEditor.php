<?php

/**
 * Moo_EditableFieldEditor is a form field that lists editable fields and allows to modify their data.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @package editablefield
 */
class Moo_EditableFieldEditor extends FormField
{
    /**
     * List of current managed editable fields.
     *
     * @var \DataList
     */
    protected $fields;

    /**
     * (non-PHPdoc).
     *
     * @see FormField::FieldHolder()
     */
    public function FieldHolder($properties = [])
    {
        return $this->renderWith('Moo_EditableFieldEditor', $properties);
    }

    /**
     * Return the fields managed by the form field.
     *
     * @return DataList
     */
    public function Fields()
    {
        if (null === $this->fields) {
            $this->fields = $this->form->getRecord()->Moo_EditableFieldEditor();
        }

        return $this->fields;
    }
}

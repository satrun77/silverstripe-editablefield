<?php

/**
 * EditableFieldMultipleOption is a base class for multiple option fields to extend.
 *
 * @see     EditableFieldCheckboxGroup, EditableFieldDropdown
 *
 * @package editablefield
 *
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @method  HasManyList Options()
 *
 * @property string $Name
 * @property string $Title
 */
class EditableFieldMultipleOption extends EditableField
{
    private static $has_many = [
        'Options' => 'EditableFieldOption',
    ];

    /**
     * Deletes all the options attached to this field before deleting the
     * field. Keeps stray options from floating around.
     *
     * @return void
     */
    public function delete()
    {
        $options = $this->Options();

        if ($options) {
            foreach ($options as $option) {
                $option->delete();
            }
        }

        parent::delete();
    }

    /**
     * Duplicate a pages content. We need to make sure all the fields attached
     * to that page go with it.
     *
     * @return DataObject
     */
    public function duplicate($doWrite = true)
    {
        $clonedNode = parent::duplicate();

        if ($this->Options()) {
            foreach ($this->Options() as $field) {
                $newField           = $field->duplicate();
                $newField->ParentID = $clonedNode->ID;
                $newField->write();
            }
        }

        return $clonedNode;
    }

    /**
     * On before saving this object we need to go through and keep an eye on
     * all our option fields that are related to this field in the form.
     *
     * @param array
     */
    public function populateFromPostData($data)
    {
        parent::populateFromPostData($data);

        // get the current options
        $fieldSet = $this->Options();

        // go over all the current options and check if ID and Title still exists
        foreach ($fieldSet as $option) {
            if (isset($data[$option->ID]) && isset($data[$option->ID]['Title']) && $data[$option->ID]['Title'] != 'field-node-deleted') {
                $option->populateFromPostData($data[$option->ID]);
            } else {
                $option->delete();
            }
        }
    }

    /**
     * Return whether or not this field has addable options such as a
     * {@link EditableDropdownField} or {@link EditableRadioField}.
     *
     * @return bool
     */
    public function getHasAddableOptions()
    {
        return true;
    }

    /**
     * Return the form field for this object in the front end form view.
     *
     * @return FormField
     */
    protected function initFormField()
    {
        $options = $this->Options()->map('EscapedTitle', 'Title');

        return new OptionsetField($this->Name, $this->Title, $options);
    }
}

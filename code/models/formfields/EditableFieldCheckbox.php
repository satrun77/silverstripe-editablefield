<?php

/**
 * Moo_EditableFieldCheckbox is an object representing a checkbox created by CMS admin.
 *
 * @package editablefield
 *
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldCheckbox extends Moo_EditableField
{
    private static $singular_name = 'Checkbox Field';
    private static $plural_name   = 'Checkboxes';

    public function getFieldConfiguration()
    {
        $options = parent::getFieldConfiguration();
        $options->push(new CheckboxField("Fields[$this->ID][CustomSettings][Default]",
                                         _t('Moo_EditableField.CHECKEDBYDEFAULT', 'Checked by Default?'),
                                         $this->getSetting('Default')));

        return $options;
    }

    protected function initFormField()
    {
        return new CheckboxField($this->Name, $this->Title, $this->getSetting('Default'));
    }
}

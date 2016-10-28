<?php

/**
 * Moo_EditableFieldCheckbox is an object representing a checkbox created by CMS admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldCheckbox extends Moo_EditableField
{
    private static $singular_name = 'Checkbox Field';
    private static $plural_name   = 'Checkboxes';

    protected $customSettingsFields = [
        'Default',
    ];

    public function getFieldConfiguration()
    {
        return [
            new CheckboxField($this->getSettingName('Default'), _t('Moo_EditableField.CHECKEDBYDEFAULT', 'Checked by Default?'), $this->getSetting('Default')),
        ];
    }

    protected function initFormField()
    {
        return new CheckboxField($this->Name, $this->Title, $this->getSetting('Default'));
    }
}

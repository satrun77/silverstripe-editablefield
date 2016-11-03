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

    /**
     * List of allowed custom settings fields.
     *
     * @var array
     */
    protected $customSettingsFields = [
        'Default',
    ];

    /**
     * Get extra configuration fields.
     *
     * @return array
     */
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

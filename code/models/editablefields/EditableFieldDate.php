<?php

/**
 * Moo_EditableFieldDate is an object representing date field created by CMS admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldDate extends Moo_EditableField
{
    private static $singular_name = 'Date Field';
    private static $plural_name   = 'Date Fields';

    /**
     * List of allowed custom settings fields.
     *
     * @var array
     */
    protected $customSettingsFields = [
        'DefaultToToday',
    ];

    /**
     * Get extra configuration fields.
     *
     * @return array
     */
    public function getFieldConfiguration()
    {
        $default = ($this->getSetting('DefaultToToday')) ? $this->getSetting('DefaultToToday') : false;
        $label   = _t('Moo_EditableField.DEFAULTTOTODAY', 'Default to Today?');

        return [
            new CheckboxField($this->getSettingName('DefaultToToday'), $label, $default),
        ];
    }

    protected function initFormField()
    {
        $defaultValue = ($this->getSetting('DefaultToToday')) ? date('Y-m-d') : $this->Default;
        $field        = new DateField($this->Name, $this->Title, $defaultValue);
        $field->setConfig('showcalendar', true);

        return $field;
    }
}

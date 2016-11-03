<?php

/**
 * Moo_EditableFieldHeading is an object representing generic heading created by CMS admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldHeading extends Moo_EditableField
{
    private static $singular_name   = 'Heading';
    private static $plural_name     = 'Headings';

    /**
     * List of allowed custom settings fields.
     *
     * @var array
     */
    protected $customSettingsFields = [
        'Level',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Remove validation tab & fields
        $fields->removeByName(['Root.Validation', 'Required', 'CustomErrorMessage']);

        return $fields;
    }

    /**
     * Get extra configuration fields.
     *
     * @return array
     */
    public function getFieldConfiguration()
    {
        $levels = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
        ];

        $level = ($this->getSetting('Level')) ? $this->getSetting('Level') : 3;
        $label = _t('Moo_EditableFieldHeading.LEVEL', 'Select Heading Level');

        return [
            new DropdownField($this->getSettingName('Level'), $label, $levels, $level),
        ];
    }

    protected function initFormField()
    {
        $labelField = new HeaderField($this->Name, $this->Title, $this->getSetting('Level'));
        $labelField->addExtraClass('FormHeading');

        return $labelField;
    }
}

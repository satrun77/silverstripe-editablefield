<?php

/**
 * EditableFieldHeading is an object representing generic heading created by CMS admin.
 *
 * @package editablefield
 *
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class EditableFieldHeading extends EditableField
{
    private static $singular_name = 'Heading';
    private static $plural_name   = 'Headings';

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
        $label = _t('EditableFieldHeading.LEVEL', 'Select Heading Level');

        $options = parent::getFieldConfiguration();

        $options->push(
            new DropdownField($this->getSettingName('Level'), $label, $levels, $level)
        );

        return $options;
    }

    protected function initFormField()
    {
        $labelField = new HeaderField($this->Name, $this->Title, $this->getSetting('Level'));
        $labelField->addExtraClass('FormHeading');

        return $labelField;
    }

    public function getFieldValidationOptions()
    {
        return false;
    }
}

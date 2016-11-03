<?php

/**
 * Moo_EditableFieldNumeric is an object representing numeric field created by CMS admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldNumeric extends Moo_EditableField
{
    private static $singular_name   = 'Numeric Field';
    private static $plural_name     = 'Numeric Fields';

    /**
     * List of allowed custom settings fields.
     *
     * @var array
     */
    protected $customSettingsFields = [
        'MinValue', 'MaxValue',
    ];

    protected function initFormField()
    {
        $field = new NumericField($this->Name, $this->Title);
        $field->addExtraClass('number');

        return $field;
    }

    /**
     * Get extra validation fields.
     *
     * @return array
     */
    public function getFieldValidationOptions()
    {
        $min = ($this->getSetting('MinValue')) ? $this->getSetting('MinValue') : '';
        $max = ($this->getSetting('MaxValue')) ? $this->getSetting('MaxValue') : '';

        return [
            new NumericField($this->getSettingName('MinValue'), _t('Moo_EditableField.MINVALUE', 'Min Value'), $min),
            new NumericField($this->getSettingName('MaxValue'), _t('Moo_EditableField.MAXVALUE', 'Max Value'), $max),
        ];
    }
}

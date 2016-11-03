<?php

/**
 * Moo_EditableFieldText is an object representing text field created by CMS admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldText extends Moo_EditableField
{
    private static $singular_name = 'Text Field';
    private static $plural_name   = 'Text Fields';

    /**
     * List of allowed custom settings fields.
     *
     * @var array
     */
    protected $customSettingsFields = [
        'MinLength', 'MaxLength', 'Rows',
    ];

    /**
     * Get extra configuration fields.
     *
     * @return array
     */
    public function getFieldConfiguration()
    {
        $min = ($this->getSetting('MinLength')) ? $this->getSetting('MinLength') : '';
        $max = ($this->getSetting('MaxLength')) ? $this->getSetting('MaxLength') : '';

        $rows = ($this->getSetting('Rows')) ? $this->getSetting('Rows') : '1';

        return [
            $learnMoreField = FieldGroup::create(
                _t('Moo_EditableFieldText.TEXTLENGTH', 'Text length'),
                new NumericField($this->getSettingName('MinLength'), 'Min', $min),
                new NumericField($this->getSettingName('MaxLength'), 'Max', $max)
            ),
            new NumericField($this->getSettingName('Rows'), _t('Moo_EditableFieldText.NUMBERROWS', 'Number of rows'), $rows),
        ];
    }

    /**
     * @return TextareaField|TextField
     */
    protected function initFormField()
    {
        if ($this->getSetting('Rows') && $this->getSetting('Rows') > 1) {
            $textareaField = new TextareaField($this->Name, $this->Title);
            $textareaField->setRows($this->getSetting('Rows'));

            return $textareaField;
        }

        return new TextField($this->Name, $this->Title, null, $this->getSetting('MaxLength'));
    }
}

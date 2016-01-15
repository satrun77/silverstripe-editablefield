<?php

/**
 * EditableFieldText is an object representing text field created by CMS admin
 *
 * @package editablefield
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class EditableFieldText extends EditableField
{
    private static $singular_name = 'Text Field';
    private static $plural_name = 'Text Fields';

    public function getFieldConfiguration()
    {
        $fields = parent::getFieldConfiguration();

        $min = ($this->getSetting('MinLength')) ? $this->getSetting('MinLength') : '';
        $max = ($this->getSetting('MaxLength')) ? $this->getSetting('MaxLength') : '';

        $rows = ($this->getSetting('Rows')) ? $this->getSetting('Rows') : '1';

        $extraFields = new FieldList(
            new FieldGroup(
                _t('EditableFieldText.TEXTLENGTH', 'Text length'),
                new NumericField($this->getSettingName('MinLength'), "", $min),
                new NumericField($this->getSettingName('MaxLength'), " - ", $max)
            ),
            new NumericField($this->getSettingName('Rows'), _t('EditableFieldText.NUMBERROWS', 'Number of rows'), $rows)
        );

        $fields->merge($extraFields);

        return $fields;
    }

    /**
     * @return TextareaField|TextField
     */
    protected function initFormField()
    {
        if ($this->getSetting('Rows') && $this->getSetting('Rows') > 1) {
            $taf = new TextareaField($this->Name, $this->Title);
            $taf->setRows($this->getSetting('Rows'));

            return $taf;
        } else {
            return new TextField($this->Name, $this->Title, null, $this->getSetting('MaxLength'));
        }
    }
}

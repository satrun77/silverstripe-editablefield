<?php

/**
 * Moo_EditableFieldTest contains test cases for test Moo_EditableField classes.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @package editablefield
 */
class Moo_EditableFieldTest extends FunctionalTest
{
    protected static $fixture_file = 'Moo_EditableFieldTest.yml';
    protected $fields              = [
        'radio-field'         => [
            'class' => 'Moo_EditableFieldRadio',
            'field' => 'OptionsetField',
        ],
        'html-field'          => [
            'class' => 'Moo_EditableFieldLiteral',
            'field' => 'LiteralField',
        ],
        'numeric-field'       => [
            'class' => 'Moo_EditableFieldNumeric',
            'field' => 'NumericField',
        ],
        'email-field'         => [
            'class' => 'Moo_EditableFieldEmail',
            'field' => 'EmailField',
        ],
        'checkbox-1'          => [
            'class' => 'Moo_EditableFieldCheckbox',
            'field' => 'CheckboxField',
        ],
        'department-dropdown' => [
            'class' => 'Moo_EditableFieldDropdown',
            'field' => 'DropdownField',
        ],
        'heading-field'       => [
            'class' => 'Moo_EditableFieldHeading',
            'field' => 'HeaderField',
        ],
        'dob-field'           => [
            'class' => 'Moo_EditableFieldDate',
            'field' => 'DateField',
        ],
        'country-field'       => [
            'class' => 'Moo_EditableFieldCountryDropdown',
            'field' => 'CountryDropdownField',
        ],
        'member-field'        => [
            'class' => 'Moo_EditableFieldMemberList',
            'field' => 'DropdownField',
        ],
        'pagetype-field'      => [
            'class' => 'Moo_EditableFieldPageTypeList',
            'field' => 'DropdownField',
        ],
        'basic-text'          => [
            'class' => 'Moo_EditableFieldText',
            'field' => 'TextField',
        ],
        'text-area'           => [
            'class' => 'Moo_EditableFieldText',
            'field' => 'TextareaField',
        ],
        'checkbox-group'      => [
            'class' => 'Moo_EditableFieldCheckboxGroup',
            'field' => 'CheckboxSetField',
        ],
    ];

    public function testGetFormField()
    {
        foreach ($this->fields as $name => $field) {
            $object = $this->objFromFixture($field['class'], $name);
            $this->assertInstanceOf($field['field'], $object->getFormField());
        }
    }

    public function testModifyingFieldSettings()
    {
        $content = 'html content 1...';
        $field   = $this->fields['html-field'];

        $htmlField = $this->objFromFixture($field['class'], 'html-field');
        $htmlField->setSetting('Content', $content);
        $htmlField->write();

        $this->assertEquals($htmlField->getSetting('Content'), $content);
    }

    public function testMultipleOptionDuplication()
    {
        $field    = $this->fields['department-dropdown'];
        $dropdown = $this->objFromFixture($field['class'], 'department-dropdown');

        $clone = $dropdown->duplicate();

        $this->assertEquals($clone->Options()->Count(), $dropdown->Options()->Count());

        foreach ($clone->Options() as $option) {
            $orginal = $dropdown->Options()->find('Title', $option->Title);

            $this->assertEquals($orginal->Sort, $option->Sort);
        }
    }
}

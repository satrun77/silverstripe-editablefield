<?php

/**
 * EditableFieldTest contains test cases for test EditableField classes
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 * @package editablefield
 */
class EditableFieldTest extends FunctionalTest {
	protected static $fixture_file = 'EditableFieldTest.yml';
	protected $fields = array(
		'radio-field' => array(
			'class' => 'EditableFieldRadio',
			'field' => 'OptionsetField'
		),
		'html-field' => array(
			'class' => 'EditableFieldLiteral',
			'field' => 'LiteralField'
		),
		'numeric-field' => array(
			'class' => 'EditableFieldNumeric',
			'field' => 'NumericField'
		),
		'email-field' => array(
			'class' => 'EditableFieldEmail',
			'field' => 'EmailField'
		),
		'checkbox-1' => array(
			'class' => 'EditableFieldCheckbox',
			'field' => 'CheckboxField'
		),
		'department-dropdown' => array(
			'class' => 'EditableFieldDropdown',
			'field' => 'DropdownField'
		),
		'heading-field' => array(
			'class' => 'EditableFieldHeading',
			'field' => 'HeaderField'
		),
		'dob-field' => array(
			'class' => 'EditableFieldDate',
			'field' => 'DateField'
		),
		'country-field' => array(
			'class' => 'EditableFieldCountryDropdown',
			'field' => 'CountryDropdownField'
		),
		'member-field' => array(
			'class' => 'EditableFieldMemberList',
			'field' => 'DropdownField'
		),
		'pagetype-field' => array(
			'class' => 'EditableFieldPageTypeList',
			'field' => 'DropdownField'
		),
		'basic-text' => array(
			'class' => 'EditableFieldText',
			'field' => 'TextField'
		),
		'text-area' => array(
			'class' => 'EditableFieldText',
			'field' => 'TextareaField'
		),
		'checkbox-group' => array(
			'class' => 'EditableFieldCheckboxGroup',
			'field' => 'CheckboxSetField'
		),
	);

	public function testGetFormField() {
		foreach($this->fields as $name => $field) {
			$object = $this->objFromFixture($field['class'], $name);
			$this->assertInstanceOf($field['field'], $object->getFormField());
		}
	}

	public function testModifyingFieldSettings() {
		$content = 'html content 1...';
		$field = $this->fields['html-field'];

		$htmlField = $this->objFromFixture($field['class'], 'html-field');
		$htmlField->setSetting('Content', $content);
		$htmlField->write();

		$this->assertEquals($htmlField->getSetting('Content'), $content);
	}

	function testMultipleOptionDuplication() {
		$field = $this->fields['department-dropdown'];
		$dropdown = $this->objFromFixture($field['class'], 'department-dropdown');

		$clone = $dropdown->duplicate();

		$this->assertEquals($clone->Options()->Count(), $dropdown->Options()->Count());

		foreach($clone->Options() as $option) {
			$orginal = $dropdown->Options()->find('Title', $option->Title);

			$this->assertEquals($orginal->Sort, $option->Sort);
		}
	}

}

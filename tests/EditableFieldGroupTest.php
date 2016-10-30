<?php

/**
 * Moo_EditableFieldGroupTest contains test cases for test Moo_EditableFieldGroup class.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldGroupTest extends FunctionalTest
{
    protected static $fixture_file = 'EditableFieldGroupTest.yml';

    public function testEmptyGroup()
    {
        $object = $this->objFromFixture('Moo_EditableFieldGroup', 'group-empty');

        $this->assertEquals('Group Empty', $object->Title);
        $this->assertEquals(0, $object->Fields()->Count());
    }

    public function testGroupFields()
    {
        $object = $this->objFromFixture('Moo_EditableFieldGroup', 'group-1');
        $fields = $object->Fields();
        $this->assertGreaterThan(0, $fields->Count());

        $cmsFields  = $object->getCMSFields();
        $gridFields = $cmsFields->dataFieldByName('Fields');

        $this->assertTrue($gridFields !== null);

        $list = $gridFields->getList();
        $this->assertEquals($list->Count(), $fields->Count());
    }

    public function testRequiredFields()
    {
        $object    = $this->objFromFixture('Moo_EditableFieldGroup', 'group-empty');
        $validator = $object->getCMSValidator();

        $this->assertInstanceOf('RequiredFields', $validator);
        $this->assertTrue($validator->fieldIsRequired('Title'));
    }
}

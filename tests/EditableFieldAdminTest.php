<?php

/**
 * Moo_EditableFieldAdminTest contains test cases for testing the LeftAndMain subclass.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldAdminTest extends FunctionalTest
{
    protected static $fixture_file = 'EditableFieldTest.yml';

    public function testPermission()
    {
        $this->logInWithPermission('EDITOR');

        $this->assertEquals('403', $this->get('/admin/editablefield')->getStatusCode());
    }

    public function testAddRequest()
    {
        $this->logInWithPermission('ADMIN');

        $response = $this->get('admin/editablefield/');
        $this->assertNotContains('Test 99', $response->getBody());

        $response = $this->post(
            'admin/editablefield/Moo_EditableField/EditForm/field/Moo_EditableField/add-multi-class/Moo_EditableFieldText',
            [
                'action_doAdd' => 1,
                'ajax'         => 1,
            ], [
                'X-Pjax' => 'CurrentForm,Breadcrumbs',
            ]
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Form_ItemEditForm_Name', $response->getBody());
        $this->assertContains('Form_ItemEditForm_Title', $response->getBody());
        $field = new Moo_EditableFieldText([
            'Name'  => 'Test 99',
            'Title' => 'Test 99',
        ]);
        $field->write();
        $response = $this->get('admin/editablefield/');
        $this->assertContains('Test 99', $response->getBody());
    }
}

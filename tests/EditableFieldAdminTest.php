<?php

/**
 * Moo_EditableFieldAdminTest contains test cases for testing the LeftAndMain subclass.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @package editablefield
 */
class Moo_EditableFieldAdminTest extends FunctionalTest
{
    protected static $fixture_file = 'Moo_EditableFieldTest.yml';

    public function testPermission()
    {
        $this->logInWithPermission('EDITOR');

        $this->assertEquals('403', $this->get('/admin/editablefield')->getStatusCode());
    }

    public function testFilterRequest()
    {
        $this->logInWithPermission('ADMIN');

        $request = new SS_HTTPRequest(null, 'admin/editablefield/filter', [
            'q'               => [
                'Term' => 'email-field',
            ],
            'action_doSearch' => 'Apply Filter',
        ]);
        $admin = new Moo_EditableFieldAdmin();
        $admin->setRequest($request);
        $response = $admin->filter();

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertContains('email-field', $response->getBody());
    }

    public function testAddRequest()
    {
        $this->logInWithPermission('ADMIN');

        $request = new SS_HTTPRequest('post', 'admin/editablefield/doAdd', null, [
            'Type'              => 'Moo_EditableFieldText',
            'action_doAddField' => 'Add',
        ]);
        $admin = new Moo_EditableFieldAdmin();
        $admin->setRequest($request);
        $response = $admin->doAdd($request);

        $this->assertInstanceOf('HTMLText', $response);
        $this->assertContains('Moo_EditableFieldText', $response->getValue());
    }

    public function testSaveRequest()
    {
        $this->logInWithPermission('ADMIN');

        $text       = $this->objFromFixture('Moo_EditableFieldText', 'basic-text');
        $email      = $this->objFromFixture('Moo_EditableFieldEmail', 'email-field');
        $textName   = $text->Name . '_update';
        $emailTitle = 'Email address 2';

        $response = $this->post('admin/editablefield/EditForm', [
            'Fields'      => [
                $text->ID  => [
                    'Name' => $textName,
                ],
                $email->ID => [
                    'Title' => $emailTitle,
                ],
            ],
            'action_save' => 'Save',
        ]);

        $this->assertEquals('200', $response->getStatusCode());

        $newText  = $this->objFromFixture('Moo_EditableFieldText', 'basic-text');
        $newEmail = $this->objFromFixture('Moo_EditableFieldEmail', 'email-field');

        $this->assertEquals($newText->Name, $textName);
        $this->assertEquals($newEmail->Title, $emailTitle);
    }

    public function testDeleteRequest()
    {
        $this->logInWithPermission('ADMIN');

        $text = $this->objFromFixture('Moo_EditableFieldText', 'basic-text');

        $response = $this->post('admin/editablefield/EditForm', [
            'Fields'        => [
                $text->ID => [],
            ],
            'action_delete' => 1,
            'delete_row'    => 1,
        ]);

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertFalse($this->getFixtureFactory()->get('Moo_EditableFieldText', 'basic-text'));
    }

    public function testAddOptionRequest()
    {
        $this->logInWithPermission('ADMIN');

        $dropdown     = $this->objFromFixture('Moo_EditableFieldDropdown', 'basic-dropdown');
        $optionsCount = $dropdown->Options()->count();

        $this->post('admin/editablefield/EditForm', [
            'action_addoptionfield' => 1,
            'Parent'                => $dropdown->ID,
        ]);

        $optionsCount2 = $dropdown->Options()->count() - 1;

        $this->assertEquals($optionsCount, $optionsCount2);
    }
}

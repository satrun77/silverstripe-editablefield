<?php

/**
 * Moo_EditableFieldEmail is an object representing email field created by CMS admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldEmail extends Moo_EditableField
{
    private static $singular_name = 'Email Field';
    private static $plural_name   = 'Email Fields';

    protected function initFormField()
    {
        return new EmailField($this->Name, $this->Title);
    }
}

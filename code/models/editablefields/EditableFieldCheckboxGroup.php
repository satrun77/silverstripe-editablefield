<?php

/**
 * Moo_EditableFieldCheckboxGroup is an object representing a set of selectable radio buttons created by CMS admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @method HasManyList Options()
 */
class Moo_EditableFieldCheckboxGroup extends Moo_EditableFieldMultipleOption
{
    private static $singular_name = 'Checkbox Group';
    private static $plural_name   = 'Checkbox Groups';

    protected function initFormField()
    {
        $optionSet = $this->Options();
        $optionMap = ($optionSet) ? $optionSet->map('EscapedTitle', 'Title') : [];

        return new CheckboxSetField($this->Name, $this->Title, $optionMap);
    }
}

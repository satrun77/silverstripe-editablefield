<?php

/**
 * EditableFieldCountryDropdown is an object representing country dropdown field created by CMS admin
 *
 * @package editablefield
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class EditableFieldCountryDropdown extends EditableField
{
    private static $singular_name = 'Country Dropdown';
    private static $plural_name = 'Country Dropdowns';

    protected function initFormField()
    {
        return new CountryDropdownField($this->Name, $this->Title);
    }

    public function getIcon()
    {
        return 'editablefield/images/formfields/editablefielddropdown.png';
    }
}

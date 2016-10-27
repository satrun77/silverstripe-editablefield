<?php

/**
 * Moo_EditableFieldMemberList is an object representing member dropdown field created by CMS admin.
 *
 * @package editablefield
 *
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldMemberList extends Moo_EditableField
{
    private static $singular_name = 'Member List Field';
    private static $plural_name   = 'Member List Fields';

    public function getFieldConfiguration()
    {
        $groupID = ($this->getSetting('GroupID')) ? $this->getSetting('GroupID') : 0;
        $groups  = DataObject::get('Group');

        if ($groups) {
            $groups = $groups->map('ID', 'Title');
        }

        $fields = new FieldList(
            new DropdownField("Fields[$this->ID][CustomSettings][GroupID]", _t('Moo_EditableField.GROUP', 'Group'), $groups,
                              $groupID)
        );

        return $fields;
    }

    protected function initFormField()
    {
        if ($this->getSetting('GroupID')) {
            $members = Member::map_in_groups($this->getSetting('GroupID'));

            return new DropdownField($this->Name, $this->Title, $members);
        }

        return false;
    }
}

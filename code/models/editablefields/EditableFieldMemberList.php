<?php

/**
 * Moo_EditableFieldMemberList is an object representing member dropdown field created by CMS admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldMemberList extends Moo_EditableField
{
    private static $singular_name   = 'Member List Field';
    private static $plural_name     = 'Member List Fields';

    /**
     * List of allowed custom settings fields.
     *
     * @var array
     */
    protected $customSettingsFields = [
        'GroupID',
    ];

    /**
     * Get extra configuration fields.
     *
     * @return array
     */
    public function getFieldConfiguration()
    {
        $groupID = ($this->getSetting('GroupID')) ? $this->getSetting('GroupID') : 0;
        $groups  = DataObject::get('Group');

        if ($groups) {
            $groups = $groups->map('ID', 'Title');
        }

        return [
            new DropdownField($this->getSettingName('GroupID'), _t('Moo_EditableField.GROUP', 'Group'), $groups, $groupID),
        ];
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

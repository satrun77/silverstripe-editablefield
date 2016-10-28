<?php

/**
 * Moo_EditableFieldOption is is a base class for fields used in dropdown or checkbox groups.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @method Moo_EditableFieldMultipleOption Parent()
 *
 * @property int    $ParentID
 * @property string $Title
 * @property string $Default
 * @property int    $Sort
 */
class Moo_EditableFieldOption extends DataObject
{
    private static $default_sort = 'Sort';
    private static $db           = [
        'Name'    => 'Varchar(255)',
        'Title'   => 'Varchar(255)',
        'Default' => 'Boolean',
        'Sort'    => 'Int',
    ];
    private static $has_one = [
        'Parent' => 'Moo_EditableFieldMultipleOption',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(['Sort', 'ParentID']);

        return $fields;
    }

    /**
     * @param Member $member
     *
     * @return bool
     */
    public function canEdit($member = null)
    {
        return ($this->Parent()->canEdit($member));
    }

    /**
     * @param Member $member
     *
     * @return bool
     */
    public function canDelete($member = null)
    {
        return ($this->Parent()->canDelete($member));
    }

    public function getEscapedTitle()
    {
        return Convert::raw2att($this->Title);
    }
}

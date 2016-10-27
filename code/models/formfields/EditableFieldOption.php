<?php

/**
 * EditableFieldOption is is a base class for fields used in dropdown or checkbox groups.
 *
 * @package editablefield
 *
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @method EditableFieldMultipleOption Parent()
 *
 * @property int    $ParentID
 * @property string $Title
 * @property string $Default
 * @property int    $Sort
 */
class EditableFieldOption extends DataObject
{
    private static $default_sort = 'Sort';
    private static $db           = [
        'Name'    => 'Varchar(255)',
        'Title'   => 'Varchar(255)',
        'Default' => 'Boolean',
        'Sort'    => 'Int',
    ];
    private static $has_one = [
        'Parent' => 'EditableFieldMultipleOption',
    ];

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

    /**
     * Template for the editing view of this option field.
     */
    public function EditSegment()
    {
        return $this->renderWith('EditableFieldOption');
    }

    /**
     * The Title Field for this object.
     *
     * @return FormField
     */
    public function TitleField()
    {
        return new TextField("Fields[{$this->ParentID}][{$this->ID}][Title]", null, $this->Title);
    }

    /**
     * Name of this field in the form.
     *
     * @return string
     */
    public function FieldName()
    {
        return "Fields[{$this->ParentID}][{$this->ID}]";
    }

    /**
     * Populate this option from the form field.
     *
     * @param array
     */
    public function populateFromPostData($data)
    {
        $this->Title   = (isset($data['Title'])) ? $data['Title'] : '';
        $this->Default = (isset($data['Default'])) ? $data['Default'] : '';
        $this->Sort    = (isset($data['Sort'])) ? $data['Sort'] : 0;
        $this->write();
    }

    public function getEscapedTitle()
    {
        return Convert::raw2att($this->Title);
    }
}

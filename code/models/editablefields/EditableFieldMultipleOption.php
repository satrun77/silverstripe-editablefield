<?php

/**
 * Moo_EditableFieldMultipleOption is a base class for multiple option fields to extend.
 *
 * @see     Moo_EditableFieldCheckboxGroup, Moo_EditableFieldDropdown
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @method  HasManyList Options()
 *
 * @property string $Name
 * @property string $Title
 */
class Moo_EditableFieldMultipleOption extends Moo_EditableField
{
    private static $has_many = [
        'Options' => 'Moo_EditableFieldOption',
    ];

    /**
     * Deletes all the options attached to this field before deleting the
     * field. Keeps stray options from floating around.
     *
     * @return void
     */
    public function delete()
    {
        $options = $this->Options();

        if ($options) {
            foreach ($options as $option) {
                $option->delete();
            }
        }

        parent::delete();
    }

    /**
     * Duplicate a pages content. We need to make sure all the fields attached
     * to that page go with it.
     *
     * @param bool $doWrite
     *
     * @return DataObject
     */
    public function duplicate($doWrite = true)
    {
        $clonedNode = parent::duplicate($doWrite);

        if ($this->Options()) {
            foreach ($this->Options() as $field) {
                $newField           = $field->duplicate();
                $newField->ParentID = $clonedNode->ID;
                $newField->write();
            }
        }

        return $clonedNode;
    }

    /**
     * Get extra configuration fields.
     *
     * @return array
     */
    public function getFieldConfiguration()
    {
        if (!$this->isInDB()) {
            $field = LiteralField::create('Options', '<p class="message notice">Once you save this field you will be able to add options</p>');
        } else {
            $config = GridFieldConfig_RelationEditor::create()
                ->addComponent(new GridFieldOrderableRows('Sort'));

            $config
                ->getComponentByType('GridFieldDataColumns')
                ->setDisplayFields([
                    'Name'    => 'Name',
                    'Title'   => 'Title',
                    'Default' => 'Default',
                ])
                ->setFieldFormatting([
                    'Default' => function ($_, Moo_EditableFieldOption $option) {
                        return $option->Default ? 'Yes' : 'No';
                    },
                ]);
            $field = GridField::create('Options', 'Options', $this->Options(), $config);
        }

        return [
            $field,
        ];
    }

    /**
     * Return the form field for this object in the front end form view.
     *
     * @return FormField
     */
    protected function initFormField()
    {
        $options = $this->Options()->map('EscapedTitle', 'Title');

        return new OptionsetField($this->Name, $this->Title, $options);
    }
}

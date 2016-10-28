<?php

/**
 * Moo_EditableFieldGroup is a data object class for editable field group.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @method ManyManyList Fields()
 */
class Moo_EditableFieldGroup extends DataObject
{
    private static $db = [
        'Title' => 'Varchar(255)',
    ];

    private static $many_many = [
        'Fields' => 'Moo_EditableField',
    ];

    private static $many_many_extraFields = [
        'Fields' => [
            'Sort' => 'Int',
        ],
    ];

    private static $default_sort = '"Title"';

    private static $singular_name = 'Group';

    private static $plural_name = 'Groups';

    /**
     * Make sure the title is required field.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields('Title');
    }

    /**
     * Returns form fields for adding/editing the data object.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Field visible on edit only
        if ($this->isInDB()) {
            $config = GridFieldConfig_RelationEditor::create();
            $config
                ->getComponentByType('GridFieldPaginator')
                ->setItemsPerPage(10);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->removeComponentsByType('GridFieldEditButton');
            $config
                ->getComponentByType('GridFieldDataColumns')
                ->setDisplayFields([
                    'Name'  => _t('Moo_EditableFieldGroup.NAME', 'Name'),
                    'Title' => _t('Moo_EditableFieldGroup.TITLE', 'Title'),
                    'Sort'  => _t('Moo_EditableFieldGroup.SORT', 'Sort'),
                ]);
            $config->addComponent(
                new GridFieldEditableManyManyExtraColumns(
                    ['Sort' => 'Int']
                ),
                'GridFieldEditButton'
            );
            $field = new GridField('Fields', 'Fields', $this->Fields(), $config);
            $fields->addFieldToTab('Root.Fields', $field);
        }

        return $fields;
    }
}

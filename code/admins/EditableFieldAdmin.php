<?php

/**
 * Moo_EditableFieldAdmin is an admin class for managing the editable fields in the system.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldAdmin extends ModelAdmin
{
    private static $url_segment = 'editablefield';
    private static $menu_title = 'Editable fields';
    private static $managed_models = [
        'Moo_EditableField',
        'Moo_EditableFieldGroup',
    ];
    private static $menu_icon = 'editablefield/images/icon.png';

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        // Customise the grid field to show icon and title
        $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
        $config = $gridField
            ->getConfig();

        $config->getComponentByType('GridFieldDataColumns')
            ->setDisplayFields([
                'Type'  => 'Type',
                'Title' => 'Title',
            ])->setFieldFormatting([
                'Type' => function ($_, Moo_EditableField $field) {
                    return $field->getIconTag();
                },
            ]);

        $adder = new GridFieldAddNewMultiClass();
        $adder->setClasses($this->getCreatableFields());
        $config->removeComponentsByType('GridFieldAddNewButton')
            ->addComponent($adder);

        return $form;
    }

    /**
     * Return a {@link ArrayList} of all the addable fields to populate the add
     * field menu.
     *
     * @return array
     */
    private function getCreatableFields()
    {
        $fields = ClassInfo::subclassesFor('Moo_EditableField');
        $output = [];

        if (!empty($fields)) {
            array_shift($fields); // get rid of subclass 0
            asort($fields); // get in order

            foreach ($fields as $field => $title) {
                // Skip an abstract class
                if ($field == 'Moo_EditableFieldMultipleOption') {
                    continue;
                }

                // Get the nice title and strip out field
                $niceTitle = _t($field . '.SINGULARNAME', $title);
                if ($niceTitle) {
                    $output[$field] = $niceTitle;
                }
            }
        }

        return $output;
    }

}

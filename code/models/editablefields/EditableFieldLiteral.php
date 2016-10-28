<?php

/**
 * Moo_EditableFieldLiteral is an object representing blank slate where you can add HTML / Images / Flash created by CMS
 * admin.
 *
 * @package editablefield
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class Moo_EditableFieldLiteral extends Moo_EditableField
{
    private static $singular_name   = 'HTML Block';
    private static $plural_name     = 'HTML Blocks';
    protected $customSettingsFields = [
        'Content',
    ];
    public function getFieldConfiguration()
    {
        $customSettings = unserialize($this->CustomSettings);
        $content        = (isset($customSettings['Content'])) ? $customSettings['Content'] : '';
        $textAreaField  = new TextareaField(
            $this->getSettingName('Content'), 'HTML', $content
        );
        $textAreaField->setRows(4);
        $textAreaField->setColumns(20);

        return [
            $textAreaField,
        ];
    }

    protected function initFormField()
    {
        $label   = $this->Title ? "<label class='left'>$this->Title</label>" : '';
        $classes = $this->Title ? '' : ' nolabel';

        return new LiteralField("LiteralField[$this->ID]", "<div id='$this->Name' class='field text$classes'>
				$label
				<div class='middleColumn literalFieldArea'>" . $this->getSetting('Content') . '</div>' .
                                                         '</div>'
        );
    }
}

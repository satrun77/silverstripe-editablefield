<?php

/**
 * Moo_EditableField is a base class for editable fields to extend.
 *
 * @package editablefield
 *
 * @author  silverstripe/userforms
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property string $Title
 * @property string $Default
 * @property int    $Required
 * @property string $Name
 * @property string $CustomErrorMessage
 * @property string $CustomSettings
 *
 * @method string ClassName()
 */
class Moo_EditableField extends DataObject
{
    /**
     * A list of CSS classes that can be added.
     *
     * @var array
     */
    public static $allowed_css = [];
    private static $db         = [
        'Name'               => 'Varchar',
        'Title'              => 'Varchar(255)',
        'Default'            => 'Varchar',
        'Required'           => 'Boolean',
        'CustomErrorMessage' => 'Varchar(255)',
        'CustomRules'        => 'Text',
        'CustomSettings'     => 'Text',
        'CustomParameter'    => 'Varchar(200)',
    ];
    private static $singular_name = 'Editable Field';
    private static $plural_name   = 'Editable Fields';

    /**
     * Instance of FormField.
     *
     * @var FormField
     */
    protected $field;

    /**
     * Template to render the field into.
     *
     * @return HTMLText
     */
    public function EditSegment()
    {
        return $this->renderWith('Moo_EditableField');
    }

    /**
     * To prevent having tables for each fields minor settings we store it as
     * a serialized array in the database.
     *
     * @return array Return all the Settings
     */
    public function getSettings()
    {
        return (!empty($this->CustomSettings)) ? unserialize($this->CustomSettings) : [];
    }

    /**
     * Set the custom settings for this field as we store the minor details in
     * a serialized array in the database.
     *
     * @param array $settings the custom settings
     */
    public function setSettings($settings = [])
    {
        $this->CustomSettings = serialize($settings);
    }

    /**
     * Set a given field setting. Appends the option to the settings or overrides
     * the existing value.
     *
     * @param string $key
     * @param string $value
     */
    public function setSetting($key, $value)
    {
        $settings       = $this->getSettings();
        $settings[$key] = $value;

        $this->setSettings($settings);
    }

    /**
     * Set the allowed css classes for the extraClass custom setting.
     *
     * @param array $allowed The permissible CSS classes to add
     */
    public function setAllowedCss(array $allowed)
    {
        if (is_array($allowed)) {
            foreach ($allowed as $k => $v) {
                self::$allowed_css[$k] = (!is_null($v)) ? $v : $k;
            }
        }
    }

    /**
     * Return just one custom setting or empty string if it does
     * not exist.
     *
     * @param string $setting
     *
     * @return string
     */
    public function getSetting($setting)
    {
        $settings = $this->getSettings();
        if (isset($settings) && count($settings) > 0) {
            if (isset($settings[$setting])) {
                return $settings[$setting];
            }
        }

        return '';
    }

    /**
     * Get the path to the icon for this field type, relative to the site root.
     *
     * @return string
     */
    public function getIcon()
    {
        return 'editablefield/images/formfields/' . strtolower($this->class) . '.png';
    }

    /**
     * Return whether or not this field has addable options
     * such as a dropdown field or radio set.
     *
     * @return bool
     */
    public function getHasAddableOptions()
    {
        return false;
    }

    /**
     * Return whether or not this field needs to show the extra options dropdown list.
     *
     * @return bool
     */
    public function showExtraOptions()
    {
        return true;
    }

    /**
     * Title field of the field in the backend of the page.
     *
     * @return TextField
     */
    public function TitleField()
    {
        return $this->getTextField('Title', 'Field title');
    }

    /**
     * Name field of the field in the backend of the page.
     *
     * @return TextField
     */
    public function NameField()
    {
        $field = $this->getTextField('Name', 'Field name');
        $field->addExtraClass('small');

        return $field;
    }

    /**
     * Generate TextField object.
     *
     * @param string $name
     * @param string $title
     *
     * @return TextField
     */
    protected function getTextField($name, $title)
    {
        $label = _t('Moo_EditableField.FIELDNAME', $title);

        $field = new TextField($name, $label, $this->getField($name));
        $field->setName($this->getFieldName($name));
        $field->addExtraClass('small');
        $field->setAttribute('placeholder', $label);

        return $field;
    }

    /**
     * Returns the Title for rendering in the front-end (with XML values escaped).
     *
     * @return string
     */
    public function getTitle()
    {
        return Convert::raw2att($this->getField('Title'));
    }

    /**
     * Return the base name for this form field in the
     * form builder. Optionally returns the name with the given field.
     *
     * @param string|bool $field
     *
     * @return string
     */
    public function getFieldName($field = false)
    {
        return ($field) ? 'Fields[' . $this->ID . '][' . $field . ']' : 'Fields[' . $this->ID . ']';
    }

    /**
     * Generate a name for the Setting field.
     *
     * @param string $field
     *
     * @return string
     */
    public function getSettingName($field)
    {
        $name = $this->getFieldName('CustomSettings');

        return $name . '[' . $field . ']';
    }

    /**
     * How to save the data submitted in this field into the database object
     * which this field represents.
     *
     * Any class's which call this should also call
     * {@link parent::populateFromPostData()} to ensure that this method is
     * called
     *
     * @param array $data
     *
     * @throws ValidationException
     */
    public function populateFromPostData($data)
    {
        $this->Title    = (isset($data['Title'])) ? $data['Title'] : '';
        $this->Default  = (isset($data['Default'])) ? $data['Default'] : '';
        $this->Required = !empty($data['Required']) ? 1 : 0;
        $this->Name     = (isset($data['Name'])) ? preg_replace('/[^a-zA-Z0-9_]+/', '',
                                                            $data['Name']) : $this->class . $this->ID;
        $this->CustomErrorMessage = (isset($data['CustomErrorMessage'])) ? $data['CustomErrorMessage'] : '';
        $this->CustomSettings     = '';

        $exists = DataObject::get($this->class)->filter('Name', $this->Name)->exclude('ID', $this->ID);
        if ($exists->count()) {
            throw new ValidationException(_t('Moo_EditableField.UNIQUENAME', 'Field name "{name}" must be unique', '',
                                             ['name' => $this->Name]));
        }

        // custom settings
        if (isset($data['CustomSettings'])) {
            $this->setSettings($data['CustomSettings']);
        }

        $this->extend('onPopulateFromPostData', $data);
        $this->write();
    }

    /**
     * Implement custom field Configuration on this field. Includes such things as
     * settings and options of a given editable form field.
     *
     * @return FieldList
     */
    public function getFieldConfiguration()
    {
        $extraClass = ($this->getSetting('ExtraClass')) ? $this->getSetting('ExtraClass') : '';

        if (is_array(self::$allowed_css) && !empty(self::$allowed_css)) {
            $cssList = [];
            foreach (self::$allowed_css as $k => $v) {
                if (!is_array($v)) {
                    $cssList[$k] = $v;
                } elseif ($k == $this->ClassName()) {
                    $cssList = array_merge($cssList, $v);
                }
            }

            $ec = new DropdownField(
                $this->getSettingName('ExtraClass'), _t('Moo_EditableField.EXTRACLASSA', 'Extra Styling/Layout'), $cssList,
                $extraClass
            );
        } else {
            $ec = new TextField(
                $this->getSettingName('ExtraClass'),
                _t('Moo_EditableField.EXTRACLASSB', 'Extra css Class - separate multiples with a space'), $extraClass
            );
        }

        $right = new TextField(
            $this->getSettingName('RightTitle'), _t('Moo_EditableField.RIGHTTITLE', 'Right Title'),
            $this->getSetting('RightTitle')
        );

        $fields = FieldList::create(
            $ec, $right
        );
        $this->extend('updateFieldConfiguration', $fields);

        return $fields;
    }

    /**
     * Append custom validation fields to the default 'Validation'
     * section in the editable options view.
     *
     * @return FieldList|false
     */
    public function getFieldValidationOptions()
    {
        $fields = new FieldList(
            new CheckboxField($this->getFieldName('Required'), _t('Moo_EditableField.REQUIRED', 'Is this field Required?'),
                              $this->Required), new TextField($this->getFieldName('CustomErrorMessage'),
                                                              _t('Moo_EditableField.CUSTOMERROR', 'Custom Error Message'),
                                                              $this->CustomErrorMessage)
        );

        $this->extend('updateFieldValidationOptions', $fields);

        return $fields;
    }

    /**
     * Return a FormField.
     *
     * @return FormField
     */
    public function getFormField()
    {
        if (null === $this->field) {
            $this->field = $this->initFormField();
        }

        return $this->field;
    }

    /**
     * Initiate a form field.
     *
     * @return FormField
     */
    protected function initFormField()
    {
        throw new DomainException(sprintf('%s must be implemented by the class %s', __METHOD__, $this->class));
    }

    /**
     * Return the error message for this field. Either uses the custom
     * one (if provided) or the default SilverStripe message.
     *
     * @return Varchar
     */
    public function getErrorMessage()
    {
        $title    = strip_tags("'" . ($this->Title ? $this->Title : $this->Name) . "'");
        $standard = _t('Form.FIELDISREQUIRED', '{name} is required', ['name' => $title]);

        // only use CustomErrorMessage if it has a non empty value
        $errorMessage = (!empty($this->CustomErrorMessage)) ? $this->CustomErrorMessage : $standard;

        return DBField::create_field('Varchar', $errorMessage);
    }
}

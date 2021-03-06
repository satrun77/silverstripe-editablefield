<?php

/**
 * Moo_EditableField is a base class for editable fields to extend.
 *
 * @author  Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * @property string $Title
 * @property string $Name
 * @property int    $Required
 * @property string $CustomErrorMessage
 * @property string $CustomSettings
 */
class Moo_EditableField extends DataObject
{
    private static $db = [
        'Name'               => 'Varchar',
        'Title'              => 'Varchar(255)',
        'Required'           => 'Boolean',
        'CustomErrorMessage' => 'Varchar(255)',
        'CustomSettings'     => 'Text',
    ];
    private static $singular_name = 'Editable Field';
    private static $plural_name   = 'Editable Fields';

    /**
     * List of fields names part of the custom settings.
     *
     * @var array
     */
    protected $customSettingsFields = [];

    /**
     * Instance of FormField.
     *
     * @var FormField
     */
    protected $field;

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
        return 'editablefield/images/formfields/' . strtolower(substr($this->class, 4)) . '.png';
    }

    /**
     * Get the icon HTML tag.
     *
     * @return string
     */
    public function getIconTag()
    {
        return '<img src="' . $this->getIcon() . '"/>';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Remove field to be recreated in separate tabs
        $fields->removeByName([
            'Required', 'CustomErrorMessage', 'CustomSettings', 'Options',
        ]);

        // Add default field configurations "custom settings"
        $fields->addFieldsToTab('Root.FieldConfiguration', [
            new TextField(
                $this->getSettingName('RightTitle'), _t('Moo_EditableField.RIGHTTITLE', 'Right Title'),
                $this->getSetting('RightTitle')
            ),
        ]);

        // Add default validation fields
        $validateFields = [
            new CheckboxField('Required', _t('Moo_EditableField.REQUIRED', 'Is this field Required?'), $this->Required),
            new TextField('CustomErrorMessage', _t('Moo_EditableField.CUSTOMERROR', 'Custom Error Message'), $this->CustomErrorMessage),
        ];
        $fields->addFieldsToTab('Root.Validation', $validateFields);

        // Add extra field configurations "custom settings" from the subclass
        if (method_exists($this, 'getFieldConfiguration')) {
            $fields->addFieldsToTab('Root.FieldConfiguration', $this->getFieldConfiguration());
        }

        // Add extra validation fields from the subclass
        if (method_exists($this, 'getFieldValidationOptions')) {
            $fields->addFieldsToTab('Root.Validation', $this->getFieldValidationOptions());
        }

        return $fields;
    }

    public function getCMSValidator()
    {
        return new RequiredFields(
            'Title', 'Name'
        );
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
     * Generate a name for the Setting field.
     *
     * @param string $field
     *
     * @return string
     */
    public function getSettingName($field)
    {
        return 'CustomSettings[' . $field . ']';
    }

    /**
     * How to save the data submitted in this field into the database object
     * which this field represents.
     *
     * Any class's which call this should also call
     * {@link parent::populateFromPostData()} to ensure that this method is
     * called
     *
     * @throws ValidationException
     */
    public function onBeforeWrite()
    {
        $return = parent::onBeforeWrite();

        // Field name must be unique
        $exists = self::get()->filter('Name', $this->Name)->exclude('ID', $this->ID);
        if ($exists->count()) {
            throw new ValidationException(
                _t('Moo_EditableField.UNIQUENAME', 'Field name "{name}" must be unique', '',
                    ['name' => $this->Name])
            );
        }

        // Filter field name from un-supported HTML attribute value
        $this->Name = preg_replace('/[^a-zA-Z0-9_]+/', '', $this->Name);

        // Get custom settings from the current object of request POST
        $customSettings = $this->getSettings();
        if (empty($customSettings)) {
            $customSettings = (array) Controller::curr()->getRequest()->postVar('CustomSettings');
        }

        // Filter out any un-supported settings by the subclasss
        if (!empty($this->customSettingsFields)) {
            $customSettings = array_intersect_key($customSettings, array_flip((array) $this->customSettingsFields));
        }

        // Set the filtered settings
        $this->setSettings($customSettings);

        return $return;
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

    public function onBeforeDuplicate(Moo_EditableField $field)
    {
        // The name field must be unique, this is temporary workaround
        $this->setField('Name', $field->Name . uniqid());
    }
}

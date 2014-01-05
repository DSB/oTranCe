<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://otrance.org
 *
 * @package         oTranCe
 * @subpackage      Translate
 * @author          Daniel Schlichtholz <admin@mysqldumper.de>
 */
/**
 * Abstract class for translation service
 *
 * @package         oTranCe
 * @subpackage      Translate
 */
abstract class Module_Translate_Service_Abstract
{
    /**
     * Module-Id.
     * Is used as unique module identifier in table module_config and must be set by extending class.
     *
     * @var string
     */
    protected $_moduleId = '';

    /**
     * The base url of the service
     *
     * @var string
     */
    protected $_serviceBaseUrl = '';

    /**
     * Will hold loaded config params
     *
     * @var Application_Model_ModuleConfig
     */
    protected $_moduleConfig;

    /**
     * Option array.
     * Will be used to receive and store adapter specific setting.
     *
     * @var array
     */
    protected $_options;

    /**
     * Constructor
     *
     * Loads module settings from database and attaches values to option array.
     */
    public function __construct()
    {
        $this->_moduleConfig = new Application_Model_ModuleConfig();
        $this->_addModuleValuesToOptions();
    }

    /**
     * Translate a message from source language into target language
     *
     * @param string $message              The message that will be translated
     * @param string $sourceLanguageLocale Locale of the language the message is given
     * @param string $targetLanguageLocale Locale of the language the message will be returned
     *
     * @return string
     */
    abstract public function getTranslation($message, $sourceLanguageLocale, $targetLanguageLocale);

    /**
     * Ask service for translatable locales
     *
     * @return array|bool array('locale1', 'locale2', ...); or false on error
     */
    abstract public function getTranslatableLocales();

    /**
     * Get options. Used in admin form to receive and store inputs from user.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set options.
     *
     * @param array $options OPtions array
     *
     * @return void
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * Load module settings from database and add value index to option array.
     *
     * @return void
     */
    protected function _addModuleValuesToOptions()
    {
        $settings = $this->_moduleConfig->getModuleSettings($this->_moduleId);
        $options  = $this->getOptions();
        foreach ($options as $index => $optionValues) {
            if ($optionValues['type'] !== 'description') {
                if (isset($settings[$index])) {
                    $options[$index]['value'] = $settings[$index];
                } else {
                    $options[$index]['value'] = $options[$index]['defaultValue'];
                }
            }
        }
        $this->setOptions($options);
    }

    /**
     * Save settings to database
     *
     * @param array $settings Settings array array('varName' => 'varValue', ..)
     *
     * @return bool
     *
     * @throws Exception
     */
    public function saveModuleSettings($settings)
    {
        $options = $this->getOptions();
        foreach ($settings as $varName => $varValue) {
            if (!isset($options[$varName])) {
                throw new Exception('VarName ' . $varName .' not set. You must add it to the options array.');
            }
            $this->_moduleConfig->setModuleSetting($this->_moduleId, $varName, $varValue);
        }
        return $this->_moduleConfig->saveModuleSettings($this->_moduleId);
    }
}

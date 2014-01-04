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
     * The base url of the service
     *
     * @var string
     */
    protected $_serviceBaseUrl = '';

    /**
     * Will hold loaded config params
     *
     * @var array
     */
    protected $config;

    /**
     * Option array.
     * Will be used to receive and store adapter specific setting.
     *
     * @var array
     */
    protected $_options;

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

}

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
     *
     * Is used to receive and store adapter specific setting. Form options in admin_translation-services is build
     * from this data.
     *
     * e.g. array(
     *  'id' => array(
     *          'type'        => 'description', // this is just an info output row shown in the form
     *          'description' => 'LANGUAGE_KEY', // Key will be translated
     *   ),
     *  'email' => array(               // if type is not "description", value will be saved to table module_config
     *           'type'         => 'text',         //valid types are 'description', 'text', 'password'
     *           'label'        => 'LANGUGAE_KEY', // output label in front of input field
     *           'description'  => 'LANGUAGE_KEY', // ouput will be place under input field
     *           'defaultValue' => '',             // set default value
     *  ), ... // more fields
     * );
     *
     * @var array
     */
    protected $_options;

    /**
     * Will hold the locales known by the service
     *
     * @var array array('en', 'de', ...)
     */
    protected $_locales = array();

    /**
     * Will hold the mapping of the oTranCe locale to service's locale
     *
     * @var array array('OTC-Locale' => 'serviceLocale', 'vi_VN' => 'vi', ...)
     */
    protected $_localeMap = array();

    /**
     * Constructor
     *
     * Loads module settings from database and attaches values to option array.
     */
    public function __construct()
    {
        $this->_moduleConfig = new Application_Model_ModuleConfig();
        $this->_getModuleSettings();
    }

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
     * Set locales.
     *
     * @param array $locales Locale array
     *
     * @return void
     */
    public function setLocales($locales)
    {
        $this->_locales = $locales;
    }

    /**
     * Get locales.
     *
     * @return array
     */
    public function getLocales()
    {
        return $this->_locales;
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
     * Load module settings from database and set properties.
     *
     * @return void
     */
    protected function _getModuleSettings()
    {
        // read all setting params of the module
        $settings = $this->_moduleConfig->getModuleSettings($this->_moduleId);

        // add saved option values to options
        $options = $this->getOptions();
        foreach ($options as $index => $optionValues) {
            if ($optionValues['type'] !== 'description') {
                if (isset($settings[$index])) {
                    $options[$index]['value'] = $settings[$index];
                } else {
                    $options[$index]['value'] = $options[$index]['defaultValue'];
                }
            }
        }

        // do we have a list of locales of this service?
        if (isset($settings['serviceLocales'])) {
            $this->_locales = $settings['serviceLocales'];
        }

        // what mappings do we know?
        if (isset($settings['localeMap'])) {
            $this->_localeMap = $settings['localeMap'];
        }

        $this->setOptions($options);
    }

    /**
     * Get mapping of oTranCe's locales to service's locales
     *
     * @return array
     */
    public function getLocaleMap()
    {
        return $this->_localeMap;
    }

    /**
     * Set locale mapping of oTranCe's locales to service's locales
     *
     * @param array $localeMap Locale mapping array('otc_locale' => 'service_locale');
     *
     * @return void
     */
    public function setLocaleMap($localeMap)
    {
        $this->_localeMap = $localeMap;
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
    public function saveSettings($settings)
    {
        $options = $this->getOptions();
        foreach ($settings as $varName => $varValue) {
            if (!in_array($varName, array('serviceLocales')) && !isset($options[$varName])
                && !in_array($varName, array('localeMap'))
            ) {
                throw new Exception('VarName ' . $varName . ' not set. You must add it to the options array.');
            }
            $this->_moduleConfig->setModuleSetting($this->_moduleId, $varName, $varValue);
            if (isset($options[$varName])) {
                $options[$varName]['value'] = $varValue;
            }
        }
        $this->setOptions($options);

        return $this->_moduleConfig->saveModuleSettings($this->_moduleId);
    }

    /**
     * Fetch data from external url using curl
     *
     * @param string $url Url to fetch
     *
     * @return bool|array
     */
    protected function _getExternalData($url)
    {
        $curlHandle = curl_init($url);
        if (is_resource($curlHandle)) {
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
            $result   = curl_exec($curlHandle);
            $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
            curl_close($curlHandle);
            if ($httpCode != 200) {
                return false;
            }
        } elseif ($result = file_get_contents($url)) {
        } else {
            $urlParts = parse_url($url);
            $host     = $urlParts['host'];
            $fp       = fsockopen($host, 80, $errno, $errstr, 30);
            if ($fp) {
                $result = '';
                $out    = "GET / HTTP/1.1\r\n";
                $out .= "Host: " . $host . " \r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);
                while (!feof($fp)) {
                    $result .= fgets($fp, 8096);
                }
                fclose($fp);
            } else {
                return false;
            }
        }

        return $result;
    }

    /**
     * Try to auto-map oTranCe's locales to the service's one.
     *
     * @param array $localesOtrance oTranCe locales as array
     *
     * @return void
     */
    public function autoMapLocales($localesOtrance)
    {
        $localesService = $this->getLocales();
        $localeMap      = $this->getLocaleMap();
        foreach ($localesOtrance as $locale) {
            if (!empty($localeMap[$locale])) {
                // there already is a mapping for this locale. Don't touch it.
                continue;
            }

            foreach ($localesService as $localeService) {
                if ($this->_localeMatches($locale, $localeService)) {
                    $localeMap[$locale] = $localeService;
                    continue 2;
                }
            }
        }

        $this->setLocaleMap($localeMap);
    }

    /**
     * Try to find out if locales match
     *
     * @param string $sourceLocale Source locale
     * @param string $targetLocale Target locale
     *
     * @return bool
     */
    protected function _localeMatches($sourceLocale, $targetLocale)
    {
        $sourceLocale = $this->_convertLocale($sourceLocale);
        $targetLocale = $this->_convertLocale($targetLocale);

        // check 1 to 1 mapping "de" => "de"
        if ($sourceLocale == $targetLocale) {
            return true;
        };

        $source = explode('-', $sourceLocale);
        $target = explode('-', $targetLocale);
        if ($source[0] == $target[0]) {
            if (isset($source[1]) && isset($target[1]) && $source[1] != $target[1]) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert locale to unified form for easier comparison
     *
     * @param string $locale
     *
     * @return string
     */
    protected function _convertLocale($locale)
    {
        $locale = strtolower($locale);
        $locale = str_replace('_', '-', $locale);

        return $locale;
    }

    /**
     * Convert locale used in oTranCe to locale known by service provider
     *
     * @param string $code Locale
     *
     * @throws Exception
     *
     * @return string
     */
    protected function _getMappedServiceLocale($code)
    {
        if (!isset($this->_localeMap[$code])) {
            throw new Exception("No mapping for '" . $code . "' found.");
        }

        return $this->_localeMap[$code];
    }

    /**
     * Get error array used in the ajax response for indicating, that source and target language are mapped
     * to the same locale.
     *
     * @return array
     */
    protected function getErrorMessageLanguagesAreEqual()
    {
        $ret = array(
            'error'    => true,
            'errorMsg' => 'The source and the target language are mapped to the same locale. '
                . ' Check the locale mapping for the translation service.'
        );

        return $ret;
    }
}

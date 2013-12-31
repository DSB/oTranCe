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
 * Class for handling translation service Google
 *
 * @package         oTranCe
 * @subpackage      Translate
 */
class Otc_Translate_Service_Google extends Otc_Translate_Service_Abstract
{

    /**
     * Constructor
     *
     * Set service specific properties on construct.
     */
    public function __construct()
    {
        $this->serviceBaseUrl = 'https://www.googleapis.com/language/translate/v2/{method}';
        $config               = Msd_Registry::getConfig();
        $this->config         = $config->getParam('google');
    }

    /**
     * Translate a message from source language into target language
     *
     * @param string $message              The message that will be translated
     * @param string $sourceLanguageLocale Locale of the language the message is given
     * @param string $targetLanguageLocale Locale of the language the message will be translated into
     *
     * @return string
     */
    public function getTranslation($message, $sourceLanguageLocale, $targetLanguageLocale)
    {
        $sourceLang     = $this->_mapLangCode($sourceLanguageLocale);
        $targetLang     = $this->_mapLangCode($targetLanguageLocale);
        $params         = array(
            'q'      => $message,
            'source' => $sourceLang,
            'target' => $targetLang,
        );
        $response       = $this->executeCall('', $params);
        $translatedText = '';
        if (isset($response->data->translations[0]->translatedText)) {
            $translatedText = $response->data->translations[0]->translatedText;
        }

        return $translatedText;
    }

    /**
     * Ask service for translatable locales
     *
     * @return array
     */
    public function getTranslatableLocales()
    {
        $ret      = array();
        $response = $this->executeCall('languages');
        if (isset($response->data->languages)) {
            $locales = $response->data->languages;
            foreach ($locales as $locale) {
                $ret[] = $locale->language;
            }
        }

        return $ret;
    }

    /**
     * Convert lang code like vi_VN into Google's code vn
     *
     * @param string $code Locale
     *
     * @return string
     */
    private function _mapLangCode($code)
    {
        $pos = strrpos($code, '_');
        if ($pos === false) {
            return $code;
        }

        return substr($code, 0, $pos);
    }

    /**
     * Execute remote method of service adn return result
     *
     * @param string $method Remote method to execute
     * @param array  $params Array of params
     *
     * @return object|bool Response object or false on error
     */
    protected function executeCall($method, $params = array())
    {
        // add api key
        $params['key'] = $this->config['apikey'];
        $url           = str_replace('{method}', $method, $this->serviceBaseUrl) . '?' . http_build_query($params);
        $handle        = @fopen($url, "r");
        if ($handle) {
            $contents = fread($handle, 4 * 4096);
            fclose($handle);
            $response = json_decode($contents);
        } else {
            $response = false;
        }

        return $response;
    }

}
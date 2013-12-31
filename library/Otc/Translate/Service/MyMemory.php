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
 * Class for handling translation service MyMemory
 *
 * @package         oTranCe
 * @subpackage      Translate
 */
class Otc_Translate_Service_MyMemory extends Otc_Translate_Service_Abstract
{
    /**
     * Constructor
     *
     * Set service specific properties on construct.
     */
    public function __construct()
    {
        $this->serviceBaseUrl = 'http://api.mymemory.translated.net/{method}';
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
            'q'        => $message,
            'langpair' => $sourceLang . '|' . $targetLang,
        );
        $response       = $this->executeCall('get', $params);
        $translatedText = '';
        if (isset($response->responseData->translatedText)) {
            $translatedText = html_entity_decode($response->responseData->translatedText);
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
        $ret = array();

        return $ret;
    }

    /**
     * Convert lang code like vi_VN to vi
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
        $url    = str_replace('{method}', $method, $this->serviceBaseUrl) . '?' . http_build_query($params);
        $handle = @fopen($url, "r");
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
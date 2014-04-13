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
class Module_Translate_Service_MyMemory extends Module_Translate_Service_Abstract
{
    /**
     * Module-Id.
     * Is used as unique module identifier in table module_config.
     *
     * @var string
     */
    protected $_moduleId = 'translate.MyMemory';

    /**
     * The base url of the service
     *
     * @var string
     */
    protected $_serviceBaseUrl = 'http://api.mymemory.translated.net/{method}';

    /**
     * Option array.
     * Will be used to receive and store adapter specific setting.
     *
     * @var array
     */
    protected $_options
        = array(
            'serviceDescription' => array(
                'type'        => 'description',
                'description' => 'L_MYMEMORY_SERVICE_DESCRIPTION',
                'logo'        => 'mymemory.jpg',
                'serviceUrl'  => 'http://mymemory.translated.net',
            ),
            'email'              => array(
                'type'         => 'text',
                'label'        => 'L_EMAIL',
                'description'  => 'L_MYMEMORY_SERVICE_EMAIL_DESCRIPTION',
                'defaultValue' => '',
            ),
            'apiKey'             => array(
                'type'         => 'password',
                'label'        => 'L_APIKEY',
                'description'  => 'L_MYMEMORY_SERVICE_ACCOUNT_DESCRIPTION',
                'defaultValue' => '',
            ),
        );

    /**
     * Translate a message from source language into target language
     *
     * @param string $message              The message that will be translated
     * @param string $sourceLanguageLocale Locale of the language the message is given
     * @param string $targetLanguageLocale Locale of the language the message will be translated into
     *
     * @return array array('error' => true/false, 'translatedText' => 'returned text', 'errorMessage' => 'returned msg')
     */
    public function getTranslation($message, $sourceLanguageLocale, $targetLanguageLocale)
    {
        $ret        = array('error' => false);
        $sourceLang = $this->_getMappedServiceLocale($sourceLanguageLocale);
        $targetLang = $this->_getMappedServiceLocale($targetLanguageLocale);
        if ($sourceLang == $targetLang) {
            return $this->getErrorMessageLanguagesAreEqual();
        }

        $params = array(
            'q'        => $message,
            'langpair' => $sourceLang . '|' . $targetLang,
            'ref'      => 'oTranCe',
        );

        $settings = $this->_moduleConfig->getModuleSettings($this->_moduleId);
        if ($settings['email'] > '') {
            $params['de'] = $settings['email'];
        }
        if ($settings['apiKey'] > '') {
            $params['key'] = $settings['email'];
        }

        $response = $this->executeCall('get', $params);
        var_dump($response);
        if ($response->responseStatus != 200) {
            $ret['error']    = true;
            $ret['errorMsg'] = $response->responseDetails;
        } else {
            if (isset($response->responseData->translatedText)) {
                $ret['translatedText'] = html_entity_decode($response->responseData->translatedText);
            }
        }

        return $ret;
    }

    /**
     * Ask service for translatable locales
     *
     * @return array
     */
    public function getTranslatableLocales()
    {
        $ret = array();
        // since MyMemory doesn't provide an api call to fetch the known locales we grab them from options array on page
        $html = $this->_getExternalData('http://mymemory.translated.net/');
        if (!$html) {
            return $ret;
        }

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $xPath = new DOMXpath($dom);
        foreach ($xPath->query("//select[@id='select_target_mm']/option/@value") as $node) {
            $ret[] = $node->nodeValue;
        }
        sort($ret, SORT_LOCALE_STRING);

        return $ret;
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
        $url    = str_replace('{method}', $method, $this->_serviceBaseUrl) . '?' . http_build_query($params);
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

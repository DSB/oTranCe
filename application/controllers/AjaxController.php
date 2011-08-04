<?php
class AjaxController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $keyId = $request->getParam('key');
        $sourceLang = $request->getParam('source');
        $targetLang = $request->getParam('target');
        $languageModel = new Application_Model_Languages();
        $this->languages = $languageModel->getAllLanguages();
        $entriesModel = new Application_Model_LanguageEntries();
        $entry = $entriesModel->getEntryById($keyId, array($sourceLang));
        $this->view->data = $this->_getTranslation(
            $entry[$sourceLang],
            $this->languages[$sourceLang]['locale'],
            $this->languages[$targetLang]['locale']
        );
    }

    /**
     * Get a Google translation
     *
     * @param  string $text
     * @param  string $sourceLang
     * @param  string $targetLang
     * @return string
     */
    private function _getTranslation($text, $sourceLang, $targetLang)
    {
        if ($text == '') {
            return '';
        }
        $sourceLang = $this->_mapLangCode($sourceLang);
        $targetLang = $this->_mapLangCode($targetLang);
        $config = Msd_Configuration::getInstance();
        $googleKey = $config->get('config.google.apikey');
        $pattern = 'https://www.googleapis.com/language/translate/v2?key=%s'
                   .'&q=%s&source=%s&target=%s' ;
        $url = sprintf($pattern, $googleKey, urlencode($text), $sourceLang, $targetLang);
        $handle = @fopen($url, "r");
        if ($handle) {
            $contents = fread($handle, 4*4096);
            fclose($handle);
        } else {
            return 'Error: not possible!';
        }
        $response = json_decode($contents);
        $data = $response->data->translations[0]->translatedText;
        return $data;
    }

    /**
     * Convert lang code like vi_VN into Googles code vn
     *
     * @param  string $code
     * @return string
     */
    private function _mapLangCode($code)
    {
        $pos = strrpos($code,'_');
        if ($pos === false) {
            return $code;
        }
        return substr($code, 0, $pos);
    }
}

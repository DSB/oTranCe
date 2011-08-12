<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Ajax Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class AjaxController extends Zend_Controller_Action
{
    /**
     * Configuration object
     * @var Msd_Cofiguration
     */
    protected $_config;

    /**
     * Languages model
     * @var Application_Model_Languages
     */
    protected $_languagesModel;

    /**
     * Languages entries model
     * @var Application_Model_Entries
     */
    protected $_entriesModel;

    /**
     * Array holding all languages
     * @var array
     */
    protected $languages;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_config         = Msd_Configuration::getInstance();
        $this->_languagesModel = new Application_Model_Languages();
        $this->languages       = $this->_languagesModel->getAllLanguages();
        $this->_entriesModel   = new Application_Model_LanguageEntries();
    }

    /**
     * Translate an entry using Google translate
     *
     * @return void
     */
    public function translateAction()
    {
        $keyId = $this->_request->getParam('key');
        $sourceLang = $this->_request->getParam('source');
        $targetLang = $this->_request->getParam('target');
        $entry = $this->_entriesModel->getEntryById($keyId, array($sourceLang));
        $this->view->data = $this->_getTranslation(
            $entry[$sourceLang],
            $this->languages[$sourceLang]['locale'],
            $this->languages[$targetLang]['locale']
        );
    }

    /**
     * Import action
     *
     * @return void
     */
    public function importKeyAction()
    {
        $userModel    = new Application_Model_User();
        $params       = $this->_request->getParams();
        $language     = $params['language'];
        $fileTemplate = $params['fileTemplate'];
        $key          = $params['key'];
        $data         = $this->_config->get('dynamic.extractedData');
        if (!isset($data[$key])) {
            return false;
        }
        $value = $data[$key];
        $ret = '';
        // check edit right for language
        $userEditRights = $userModel->getUserEditRights();
        if (!in_array($language, $userEditRights)) {
            //user is not allowed to edit this language
            $ret .= $this->view->getIcon('Attention', '', 16). ' You are not allowed to edit this language!';
        }

        if (!$this->_entriesModel->hasEntryWithKey($key)) {
            //new entry - check rights
            if (!$userModel->hasRight('addVar')) {
                $ret .= $this->view->getIcon('Attention', '', 16). ' You are not allowed to add new entries!';
            } else {
                $this->_entriesModel->saveNewKey($key, $fileTemplate);
            }
        }

        if ($ret == '') {
            // everything ok - we can save the value
            // get Key id
            $entry = $this->_entriesModel->getEntryByKey($key);
            $keyId = $entry['id'];
            $res = $this->_entriesModel->saveEntries($keyId, array($language => $value));
            if ($res === true) {
                $ret .= $this->view->getIcon('Ok', '', 16);
            } else {
                $ret .= $this->view->getIcon('Attention', '', 16). $res;
            }
        }
        $this->view->message = $ret;
        //print_r($params);
    }

    /**
     * Get a Google translation
     *
     * @param  string $text
     * @param  string $sourceLang
     * @param  string $targetLang
     *
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
     *
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

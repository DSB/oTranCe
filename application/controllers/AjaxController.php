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
     * User model
     * @var Application_Model_User
     */
    protected $_userModel;

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
        $this->_userModel      = new Application_Model_User();
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
     * Import action.
     * Expects array of language entry keys as param in request and returns an array(key => status).
     * Status:
     *  0 = technical error
     *  1 = saved successfully
     *  2 = user has no edit right for this language
     *  3 = user is not allowed to add this new entry
     *
     * @return void
     */
    public function importKeyAction()
    {
        $ret = array();
        $params       = $this->_request->getParams();
        $language     = $params['language'];
        $fileTemplate = $params['fileTemplate'];
        $keys         = $params['keys'];
        $this->_data  = $this->_config->get('dynamic.extractedData');
        $i = 0;
        foreach ($keys as $key) {
            $res = $this->_saveKey($key, $fileTemplate, $language);
            $ret[$i] = array('key' => $key, 'result' => $res);
            $i++;
        }
        $this->view->data = $ret;
    }

    /**
     * Save a key and it's value to the database.
     *
     * @param $key
     * @param $fileTemplate
     * @param $language
     *
     * @return int
     */
    private function _saveKey($key, $fileTemplate, $language)
    {
        $value = $this->_data[$key];
        // check edit right for language
        $userEditRights = $this->_userModel->getUserEditRights();
        if (!in_array($language, $userEditRights)) {
            //user is not allowed to edit this language
            return 2;
        }

        if (!$this->_entriesModel->hasEntryWithKey($key)) {
            //it is a new entry - check rights
            if (!$this->_userModel->hasRight('addVar')) {
                return 3;
            } else {
                // user is allowed to add new keys -> create it
                $this->_entriesModel->saveNewKey($key, $fileTemplate);
            }
        }

        // ok - we can save the value -> key id
        $entry = $this->_entriesModel->getEntryByKey($key);
        $keyId = $entry['id'];
        $res = $this->_entriesModel->saveEntries($keyId, array($language => $value));
        if ($res === true) {
            return 1;
        } else {
            return 0;
        }
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

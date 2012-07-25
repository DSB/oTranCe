<?php
require_once 'SettingsController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers_Settings
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Settings Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Settings
 */
class Settings_LanguagesController extends SettingsController
{
    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $languagesModel        = new Application_Model_Languages();
        $this->view->languages            = $languagesModel->getAllLanguages();
        $this->view->fallbackLanguageId   = $languagesModel->getFallbackLanguageId();
        $this->view->refLanguagesSelected = $this->getRefLanguageSettings();
        $this->view->editLanguages        = $this->_userModel->getUserLanguageRights();
    }

    /**
     * Save list of reference languages
     *
     * @param int    $recordsPerPage    Records per Page
     * @param string $interfaceLanguage Locale of the interface's language
     *
     * @return boolean
     */
    public function saveUserSettings($recordsPerPage, $interfaceLanguage)
    {
        $this->_dynamicConfig->setParam('recordsPerPage', $recordsPerPage);
        $res  = $this->_userModel->saveSetting('recordsPerPage', $recordsPerPage);
        $res &= $this->_userModel->saveSetting('interfaceLanguage', $interfaceLanguage);

        return $res;
    }

    /**
     * Get list of reference languages
     *
     * @return boolean
     */
    public function getRefLanguageSettings()
    {
        $res = $this->_userModel->loadSetting('referenceLanguage', '', true);
        return $res;
    }

}

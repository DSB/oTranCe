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
 * Settings Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class SettingsController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_User
     */
    private $_userModel;

    /**
     * Instance of Msd_Crypt
     *
     * @var Msd_Crypt
     */
    private $_crypt = null;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_userModel = new Application_Model_User();
    }

    const VCS_SAVE_SUCCESS = 0x00;
    const VCS_PASS_NOT_EQUAL = 0x01;

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $languagesModel = new Application_Model_Languages();
        $this->view->languages = $languagesModel->getAllLanguages();
        if ($this->_request->isPost()) {
            $languagesSelected = $this->_request->getParam('selLangs', array());
            $recordsPerPage = $this->_request->getParam('recordsPerPage', 20);
            $vcsUser = $this->_request->getParam('vcsUser');
            //save new settings to session
            $config = Msd_Configuration::getInstance();
            $config->set('dynamic.recordsPerPage', $recordsPerPage);

            $saved = $this->saveUserSettings($languagesSelected, $recordsPerPage);
            $saveVcsCredsResult = $this->_saveVcsCredentials();
            $saved = $saved && ($saveVcsCredsResult == self::VCS_SAVE_SUCCESS);
            $this->view->saved = $saved;
        } else {
            $recordsPerPage = $this->_userModel->loadSetting('recordsPerPage', 10);
            $languagesSelected = $this->getRefLanguageSettings();
            $vcsUser = $this->_getVcsUser();
        }
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int) $recordsPerPage);
        $this->view->refLanguagesSelected = $languagesSelected;
        $this->view->editLanguages = $this->_userModel->getUserEditRights();
        $this->view->vcsUser = $vcsUser;
    }

    /**
     * Save list of reference languages
     *
     * @param array $languagesSelected
     * @param int   $recordsPerPage
     *
     * @return boolean
     */
    public function saveUserSettings($languagesSelected, $recordsPerPage)
    {
        $res = $this->_userModel->saveSetting('recordsPerPage', $recordsPerPage);
        $res &= $this->_userModel->saveSetting('referenceLanguage', $languagesSelected);
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

    private function _saveVcsCredentials()
    {
        if ($this->_crypt === null) {
            $this->_initCrypt();
        }
        $vcsUser = $this->_request->getParam('vcsUser');
        if ($vcsUser !== null && strlen($vcsUser) > 0) {
            $vcsPass = $this->_request->getParam('vcsPass');
            $vcsPass2 = $this->_request->getParam('vcsPass2');
            if ($vcsPass != $vcsPass2) {
                return self::VCS_PASS_NOT_EQUAL;
            }
            $encrypted = $this->_crypt->encrypt($vcsUser . '%@%' . $vcsPass);
            $this->_userModel->saveSetting('vcsCredentials', $encrypted);
        } else {
            $this->_userModel->deleteSetting('vcsCredentials');
        }
        return self::VCS_SAVE_SUCCESS;
    }

    private function _getVcsUser()
    {
        if ($this->_crypt === null) {
            $this->_initCrypt();
        }
        $cryptedVcsCreds = $this->_userModel->loadSetting('vcsCredentials', null);
        if ($cryptedVcsCreds !== null) {
            $vcsCredentials = $this->_crypt->decrypt($cryptedVcsCreds);
            list ($vcsUser, ) = explode('%@%', $vcsCredentials);
            return $vcsUser;
        }

        return null;
    }

    private function _initCrypt()
    {
        $config = Msd_Configuration::getInstance();
        $encKey = $config->get('config.project.encryptionKey');
        $this->_crypt = new Msd_Crypt($encKey);
    }
}

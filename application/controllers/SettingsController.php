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
class SettingsController extends Msd_Controller_Action
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
        if (!$this->_userModel->hasRight('editConfig')) {
            $this->_redirect('/');
        }
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
        if ($this->_request->isPost()) {
            $languagesSelected = $this->_request->getParam('selLangs', array());
            $recordsPerPage    = $this->_request->getParam('recordsPerPage', 20);
            $saved             = $this->saveUserSettings($languagesSelected, $recordsPerPage);
            $this->_dynamicConfig->setParam('recordsPerPage', $recordsPerPage);

            $projectConfig = $this->_config->getParam('project');
            if ($projectConfig['vcsActivated'] == 1) {
                $vcsUser            = $this->_request->getParam('vcsUser');
                $saveVcsCredsResult = $this->_saveVcsCredentials();
                $saved              = $saved && ($saveVcsCredsResult == self::VCS_SAVE_SUCCESS);
            }
            $interfaceLanguage = $this->_request->getParam('interfaceLanguage', 'en');
            $this->_userModel->saveSetting('interfaceLanguage', $interfaceLanguage);
            $this->view->saved = $saved;
        } else {
            $recordsPerPage    = $this->_userModel->loadSetting('recordsPerPage', 10);
            $languagesSelected = $this->getRefLanguageSettings();
            $vcsUser           = $this->_getVcsUser();
        }

        $languagesModel                   = new Application_Model_Languages();
        $this->view->languages            = $languagesModel->getAllLanguages();
        $this->view->fallbackLanguageId   = $languagesModel->getFallbackLanguage();
        $this->view->selRecordsPerPage    = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int) $recordsPerPage);
        $this->view->refLanguagesSelected = $languagesSelected;
        $this->view->editLanguages        = $this->_userModel->getUserLanguageRights();
        $languageConfig                   = Msd_Language::getInstance();
        $availableLanguages               = $languageConfig->getAvailableLanguages();
        $this->view->selInterfaceLanguage = Msd_Html::getHtmlOptionsFromAssocArray(
            $availableLanguages,
            'locale',
            '{locale} - {name}',
            $this->_userModel->loadSetting('interfaceLanguage'),
            false
        );
        if (isset($vcsUser)) {
            $this->view->vcsUser = $vcsUser;
        }
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
        $res  = $this->_userModel->saveSetting('recordsPerPage', $recordsPerPage);
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

    /**
     * Delete the user specific VCS credentials.
     *
     * @return void
     */
    public function deleteCredentialsAction()
    {
        $this->_userModel->deleteSetting('vcsCredentials');
        $this->_forward('index');
    }

    /**
     * Save the user specific VCS credentials.
     *
     * @return int
     */
    private function _saveVcsCredentials()
    {
        if ($this->_crypt === null) {
            $this->_initCrypt();
        }
        $vcsUser = $this->_request->getParam('vcsUser');
        if ($vcsUser !== null && strlen($vcsUser) > 0) {
            $vcsPass        = $this->_request->getParam('vcsPass');
            $vcsPassConfirm = $this->_request->getParam('vcsPass2');
            if ($vcsPass != $vcsPassConfirm) {
                return self::VCS_PASS_NOT_EQUAL;
            }
            $encrypted = $this->_crypt->encrypt($vcsUser . '%@%' . $vcsPass);
            $this->_userModel->saveSetting('vcsCredentials', $encrypted);
            return self::VCS_SAVE_SUCCESS;
        }
        return self::VCS_SAVE_SUCCESS;
    }

    /**
     * Retrives the user specific VCS username.
     *
     * @return string|null
     */
    private function _getVcsUser()
    {
        if ($this->_crypt === null) {
            $this->_initCrypt();
        }
        $cryptedVcsCreds = $this->_userModel->loadSetting('vcsCredentials', null);
        if ($cryptedVcsCreds !== null) {
            $vcsCredentials   = $this->_crypt->decrypt($cryptedVcsCreds);
            list ($vcsUser, ) = explode('%@%', $vcsCredentials);
            return $vcsUser;
        }

        return null;
    }

    /**
     * Iniotialize class for en- and decryption.
     *
     * @return void
     */
    private function _initCrypt()
    {
        $projectConfig = $this->_config->getParam('project');
        $this->_crypt = new Msd_Crypt($projectConfig['encryptionKey']);
    }
}

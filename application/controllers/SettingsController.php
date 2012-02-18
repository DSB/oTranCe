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

    const VCS_SAVE_SUCCESS   = 0x00;
    const VCS_PASS_NOT_EQUAL = 0x01;

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $languagesModel = new Application_Model_Languages();
        $languageConfig = Msd_Language::getInstance();

        if ($this->_request->isPost()) {
            $recordsPerPage        = $this->_request->getParam('recordsPerPage', 20);
            $userInterfaceLanguage = $this->_userModel->loadSetting('interfaceLanguage');
            $interfaceLanguage     = $this->_request->getParam('interfaceLanguage', $userInterfaceLanguage);
            $saved                 = $this->saveUserSettings($recordsPerPage, $interfaceLanguage);

            $projectConfig = $this->_config->getParam('project');
            if ($projectConfig['vcsActivated'] == 1) {
                $vcsUser            = $this->_request->getParam('vcsUser');
                $saveVcsCredsResult = $this->_saveVcsCredentials();
                $saved              = $saved && ($saveVcsCredsResult == self::VCS_SAVE_SUCCESS);
            }
            $this->view->saved = $saved;
        } else {
            $recordsPerPage    = $this->_userModel->loadSetting('recordsPerPage', 10);
            $vcsUser           = $this->_getVcsUser();
        }
        $this->view->languages            = $languagesModel->getAllLanguages();
        $this->view->fallbackLanguageId   = $languagesModel->getFallbackLanguage();
        $this->view->selRecordsPerPage    = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int) $recordsPerPage);
        $this->view->refLanguagesSelected = $this->getRefLanguageSettings();
        $this->view->editLanguages        = $this->_userModel->getUserLanguageRights();
        $interfaceLanguage                = $this->_userModel->loadSetting('interfaceLanguage');
        $availableLanguages               = $languageConfig->getAvailableLanguages();
        $this->view->selInterfaceLanguage = Msd_Html::getHtmlOptionsFromAssocArray(
            $availableLanguages,
            'locale',
            '{locale} - {name}',
            $interfaceLanguage,
            false
        );
        $languageConfig->loadLanguage($interfaceLanguage);

        if (isset($vcsUser)) {
            $this->view->vcsUser = $vcsUser;
        }
    }

    /**
     * Save list of reference languages
     *
     * @param int    $recordsPerPage
     * @param string Locale of language
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

    /**
     * Delete user specific VCS credentials.
     *
     * @return void
     */
    public function deleteCredentialsAction()
    {
        $this->_userModel->deleteSetting('vcsCredentials');
        $this->_forward('index');
    }

    /**
     * Save user specific VCS credentials.
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
     * Get user specific VCS username.
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
     * Initialize class for en- and decryption.
     *
     * @return void
     */
    private function _initCrypt()
    {
        $projectConfig = $this->_config->getParam('project');
        $this->_crypt = new Msd_Crypt($projectConfig['encryptionKey']);
    }
}

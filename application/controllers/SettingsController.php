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

    const VCS_SAVE_SUCCESS   = 0x00;
    const VCS_PASS_NOT_EQUAL = 0x01;

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $languagesModel        = new Application_Model_Languages();
        $interfaceLanguage = $this->_dynamicConfig->getParam('interfaceLanguage', false);
        $userInterfaceLanguage = $this->_userModel->loadSetting('interfaceLanguage');
        $languageConfig = Msd_Language::getInstance($interfaceLanguage);

        if ($this->_request->isPost()) {
            $recordsPerPage        = $this->_request->getParam('recordsPerPage', 20);
            $interfaceLanguage     = $this->_request->getParam('interfaceLanguage', $interfaceLanguage);
            $this->_dynamicConfig->setParam('interfaceLanguage', $interfaceLanguage);
            $saved = $this->saveUserSettings($recordsPerPage, $interfaceLanguage);
            $userInterfaceLanguage = $this->_userModel->loadSetting('interfaceLanguage');
            $languageConfig->loadLanguage($interfaceLanguage);

            $projectConfig = $this->_config->getParam('project');
            if ($projectConfig['vcsActivated'] == 1) {
                $vcsUser            = $this->_request->getParam('vcsUser');
                $saveVcsCredsResult = $this->_saveVcsCredentials();
                $saved              = $saved && ($saveVcsCredsResult == self::VCS_SAVE_SUCCESS);
            }

            $oldPassword = $this->_request->getParam('oldPassword');
            $newPassword = $this->_request->getParam('newPassword');
            $newPasswordConfirm = $this->_request->getParam('newPasswordConfirm');
            if (
                isset($oldPassword, $newPassword, $newPasswordConfirm)
                && !empty($oldPassword)
                && !empty($newPassword)
                && !empty($newPasswordConfirm)
            ) {
                $saved = $saved && $this->_changePassword($oldPassword, $newPassword, $newPasswordConfirm);
            }
            $this->view->saved = (bool) $saved;
        } else {
            $recordsPerPage    = $this->_userModel->loadSetting('recordsPerPage', 10);
            $vcsUser           = $this->_getVcsUser();
        }
        $this->view->languages            = $languagesModel->getAllLanguages();
        $this->view->fallbackLanguageId   = $languagesModel->getFallbackLanguage();
        $this->view->selRecordsPerPage    = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int) $recordsPerPage);
        $this->view->refLanguagesSelected = $this->getRefLanguageSettings();
        $this->view->editLanguages        = $this->_userModel->getUserLanguageRights();

        $availableLanguages               = $languageConfig->getAvailableLanguages();
        $this->view->selInterfaceLanguage = Msd_Html::getHtmlOptionsFromAssocArray(
            $availableLanguages,
            'locale',
            '{locale} - {name}',
            $userInterfaceLanguage,
            false
        );

        $languageConfig->loadLanguage($interfaceLanguage);
        $this->view->lang = $languageConfig;
        if (isset($vcsUser)) {
            $this->view->vcsUser = $vcsUser;
        }
    }

    /**
     * Helper method for changing the new user password.
     *
     * @param string $oldPassword        Old password.
     * @param string $newPassword        New password.
     * @param string $newPasswordConfirm Password confirmation.
     *
     * @return bool
     */
    protected function _changePassword($oldPassword, $newPassword, $newPasswordConfirm)
    {
        if ($newPassword != $newPasswordConfirm) {
            return false;
        }

        /**
         * @var Zend_Controller_Request_Http $request
         */
        $request = $this->getRequest();

        $result = $this->_userModel->changePassword($oldPassword, $newPassword);
        $cookie = $request->getCookie('oTranCe_autologin');
        if ($result && $cookie !== null && !empty($cookie)) {
            $auth = Zend_Auth::getInstance()->getIdentity();
            $user = new Msd_User();
            $user->setLoginCookie($auth['name'], $newPassword);
        }
        return $result;
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

    /**
     * Delete user specific VCS credentials.
     *
     * @return void
     */
    public function deleteCredentialsAction()
    {
        $this->view->saved = $this->_userModel->deleteSetting('vcsCredentials');
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

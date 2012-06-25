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
class Settings_VcsController extends SettingsController
{
    const VCS_SAVE_SUCCESS   = 0x00;
    const VCS_PASS_NOT_EQUAL = 0x01;

    /**
     * Instance of Msd_Crypt
     *
     * @var Msd_Crypt
     */
    protected $_crypt = null;

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        if ($this->_request->isPost()) {
            $saved             = $this->_saveVcsCredentials();
            $this->view->saved = ($saved == Settings_VcsController::VCS_SAVE_SUCCESS) ? true : false;
        }
        $this->view->vcsUser = $this->_getVcsUser();
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
            $vcsCredentials = $this->_crypt->decrypt($cryptedVcsCreds);
            list ($vcsUser,) = explode('%@%', $vcsCredentials);
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
        $this->_crypt  = new Msd_Crypt($projectConfig['encryptionKey']);
    }
}

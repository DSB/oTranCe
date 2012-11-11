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
            if ($this->_validateInputs() == true) {
                $this->view->saved = $this->_saveVcsCredentials();
            }
        }
        $this->view->vcsUser = $this->_getVcsUser();
    }

    /**
     * Validate user input and set error messages for view
     *
     * @return bool
     */
    protected function _validateInputs()
    {
        $vcsUser        = $this->_request->getParam('vcsUser');
        $vcsPass        = $this->_request->getParam('vcsPass');
        $vcsPassConfirm = $this->_request->getParam('vcsPass2');
        $translator     = Msd_Language::getInstance();

        // Check user name has 2-50 chars
        $messages       = array();
        $strLenValidate = new Zend_Validate_StringLength(array('min' => 2, 'max' => 50));
        if (!$strLenValidate->isValid($vcsUser)) {
            $messages['vcsUser'] = $translator->translateZendMessageIds($strLenValidate->getMessages());
        }

        // check both passwords are equal
        $identicalValidate = new Zend_Validate_Identical($vcsPass);
        if (!$identicalValidate->isValid($vcsPassConfirm)) {
            $messages['vcsPass'] = $translator->translateZendMessageIds($identicalValidate->getMessages());
        }

        if (sizeof($messages) > 0) {
            $this->view->errors = $messages;
            return false;
        }
        return true;
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
     * @return bool
     */
    private function _saveVcsCredentials()
    {
        if ($this->_crypt === null) {
            $this->_initCrypt();
        }
        $vcsUser = $this->_request->getParam('vcsUser');
        $vcsPass = $this->_request->getParam('vcsPass');
        if ($vcsUser !== null && strlen($vcsUser) > 0) {
            $encrypted = $this->_crypt->encrypt($vcsUser . '%@%' . $vcsPass);
            $this->_userModel->saveSetting('vcsCredentials', $encrypted);
            return true;
        }
        return false;
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

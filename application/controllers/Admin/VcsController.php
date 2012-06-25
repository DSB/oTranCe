<?php
require_once 'AdminController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Vcs Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 */
class Admin_VcsController extends AdminController
{
    /**
     * Instance of Msd_Crypt
     *
     * @var Msd_Crypt
     */
    protected $_crypt = null;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        if (!$this->_userModel->hasRight('editVcs')) {
            $this->_redirect('/');
        }
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $vcsConf = $this->_config->getParam('vcs');
        if ($this->_request->isPost()) {
            if ($this->_request->getParam('saveButton') !== null) {
                $this->_saveVcsConfig();
                $vcsConf = $this->_config->getParam('vcs');
            } else {
                $vcsConf['adapter'] = $this->_request->getParam('vcsAdapter');
            }
        }
        $this->view->vcsAdapterParams = Msd_Vcs::getAdapterOptions($vcsConf['adapter']);
        $this->view->vcsConfig        = $vcsConf;
        $this->view->vcsAvailAdapter  = Msd_Vcs::getAvailableAdapter();
    }

    /**
     * Saves the config for VCS.
     *
     * @return void
     */
    private function _saveVcsConfig()
    {
        $vcsConfig = array(
            'adapter'       => $this->_request->getParam('vcsAdapter'),
            'commitMessage' => $this->_request->getParam('vcsCommitMessage'),
            'options'       => $this->_request->getParam('vcsOptions'),
        );
        $this->_config->setParam('vcs', $vcsConfig);

        $projectConfig                 = $this->_config->getParam('project');
        $projectConfig['vcsActivated'] = (int) $this->_request->getParam('vcsActivated', 0);
        $this->_config->setParam('project', $projectConfig);
        $this->view->saved = $this->_config->save();
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
     * Save general project VCS credentials.
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

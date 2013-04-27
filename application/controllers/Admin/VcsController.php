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
     * Check general access right
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        $this->checkRight('editVcs');
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
            'adapter'            => $this->_request->getParam('vcsAdapter'),
            'commitMessage'      => $this->_request->getParam('vcsCommitMessage'),
            'copyToCOPath'       => $this->_request->getParam('vcsCopyToCOPath'),
            'revertBeforeUpdate' => $this->_request->getParam('vcsRevertBeforeUpdate'),
            'options'            => $this->_request->getParam('vcsOptions'),
        );
        $this->_config->setParam('vcs', $vcsConfig);

        $projectConfig                 = $this->_config->getParam('project');
        $projectConfig['vcsActivated'] = (int)$this->_request->getParam('vcsActivated', 0);
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

}

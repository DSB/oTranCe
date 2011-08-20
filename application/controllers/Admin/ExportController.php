<?php
require_once 'AdminController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Export Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class Admin_ExportController extends AdminController
{
    public function indexAction()
    {
        $vcsConf = $this->_config->getParam('vcs');
        if ($this->_request->isPost()) {
            if ($this->_request->getParam('saveButton') !== null) {
                $this->_saveSvnConfig();
                $vcsConf = $config->getParam('vcs');
            } else {
                $vcsConf['adapter'] = $this->_request->getParam('vcsAdapter');
            }
        }
        $this->view->vcsAdapterParams = Msd_Vcs::getAdapterOptions($vcsConf['adapter']);
        $this->view->vcsConfig = $vcsConf;
        $this->view->vcsAvailAdapter = Msd_Vcs::getAvailableAdapter();

        $langModel = new Application_Model_Languages();
        $this->view->languages = $langModel->getAllLanguages();
        $this->view->fallbackLang = $langModel->getFallbackLanguage();
    }

    private function _saveSvnConfig()
    {
        $subversionConf = array(
            'adapter' => $this->_request->getParam('vcsAdapter'),
            'commitMessage' => $this->_request->getParam('vcsCommitMessage'),
            'options' => $this->_request->getParam('vcsOptions'),
        );
        $this->_config->setParam('vcs', $subversionConf);

        $langModel = new Application_Model_Languages();
        $langModel->setFallbackLanguage($this->_request->getParam('fallbackLang'));

        $this->_config->save();
    }

}

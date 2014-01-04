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
class Admin_TranslationServicesController extends AdminController
{
    /**
     * Check general access right
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->checkRight('editTlServices');
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        //$vcsConf = $this->_config->getParam('vcs');
        if ($this->_request->isPost()) {
            if ($this->_request->getParam('saveButton') !== null) {
                //$this->_saveVcsConfig();
                //$vcsConf = $this->_config->getParam('vcs');
            } else {
                //$vcsConf['adapter'] = $this->_request->getParam('vcsAdapter');
            }
        }
        //$this->view->vcsAdapterParams = Msd_Vcs::getAdapterOptions($vcsConf['adapter']);
        //$this->view->vcsConfig        = $vcsConf;
        $this->_addAdapterLanguageFile('MyMemory');
        $servicesBasePath = realpath(APPLICATION_PATH . '/../modules/library/Translate/Service/');
        $this->view->tsAvailAdapter = Msd_Translate::getAvailableTranslationServices($servicesBasePath);
    }

    /**
     * Add adapter specific language file
     *
     * @param string $adapterName Name of adapter
     *
     * @return void
     */
    protected function _addAdapterLanguageFile($adapterName)
    {
        $languageFile = $adapterName . '.php';
        $language     = Msd_Language::getInstance();
        $language->addTranslationFile(APPLICATION_PATH . '/../modules/library/Translate/languages', $languageFile);
    }

}

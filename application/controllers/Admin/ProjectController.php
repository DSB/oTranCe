<?php
require_once 'AdminController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev: 1569 $
 * @author          $Author: dsb $
 */
/**
 * Admin/Project Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class Admin_ProjectController extends AdminController
{
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        if (!$this->_userModel->hasRight('editProject')) {
            $this->_redirect('/');
        }
    }

    /**
     * Index action - show project settings
     *
     * @return void
     */
    public function indexAction()
    {
        /**
         * @var Zend_Controller_Request_Http $request
         */
        $request       = $this->_request;
        $languageModel = new Application_Model_Languages();
        if ($request->isPost()) {
            $projectSettings                        = $this->_config->getParam('project');
            $projectSettings['name']                = $this->_request->getParam('name');
            $projectSettings['url']                 = $this->_request->getParam('url');
            $projectSettings['logo']                = $this->_request->getParam('logo');
            $projectSettings['translateToFallback'] = $this->_request->getParam('translateToFallback', 0);
            $this->_config->setParam('project', $projectSettings);
            $languageModel->setFallbackLanguage($this->_request->getParam('fallbackLang'));

            $this->_config->save();
        }
        $this->view->settings           = $this->_config->getParam('project');
        $this->view->fallbackLanguageId = $languageModel->getFallbackLanguage();
    }
}

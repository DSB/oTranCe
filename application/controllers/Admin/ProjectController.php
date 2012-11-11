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
 * Admin/Project Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
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
        if (!$this->_userModel->hasRight('admTabProject')) {
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
        if ($request->isPost() && $this->_userModel->hasRight('editProject')) {
            $projectSettings                        = $this->_config->getParam('project');
            $projectSettings['name']                = $this->_request->getParam('name');
            $projectSettings['url']                 = $this->_request->getParam('url');
            $projectSettings['email']               = $this->_request->getParam('email');
            $projectSettings['logo']                = $this->_request->getParam('logo');
            $projectSettings['translateToFallback'] = $this->_request->getParam('translateToFallback', 0);
            $this->_config->setParam('project', $projectSettings);
            $languageModel->setFallbackLanguage($this->_request->getParam('fallbackLang'));

            $this->view->saved = $this->_config->save();
        }
        $projectSettings = $this->_config->getParam('project');
        // fallback for older installations
        if (empty($this->projectSettings['email'])) {
            $this->projectSettings['email'] = '';
        }
        $this->view->settings           = $projectSettings;
        $this->view->fallbackLanguageId = $languageModel->getFallbackLanguageId();

        // if user does not have edit rights just show the information
        if (!$this->_userModel->hasRight('editProject')) {
            $this->_helper->viewRenderer('view');
        }
    }
}

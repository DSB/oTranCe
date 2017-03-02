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
     * Check general access right
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        $this->checkRight('admTabProject');
    }

    /**
     * Index action - show project settings
     *
     * @return void
     */
    public function indexAction()
    {
        $projects = $this->getAllProjectsConfig();
        $this->view->projects = $projects;
    }

    /**
     * Index action - show project settings
     *
     * @return void
     */
    public function editAction()
    {
        /**
         * @var Zend_Controller_Request_Http $request
         */
        $request       = $this->_request;
        $languageModel = new Application_Model_Languages();
        $projectId = $this->getProjectId();
        if ($request->isPost() && $this->_userModel->hasRight('editProject')) {

            $projectSettings = $this->getProjectSettingsArray();
            $allProjects = $this->getAllProjectsConfig();
            $allProjects[$projectId] = $projectSettings;
            $this->_config->setParam('project', $allProjects);
            $languageModel->setFallbackLanguage($this->_request->getParam('fallbackLang'));

            $this->view->saved = $this->_config->save();
        }

        $projectSettings = $this->getProjectConfig($projectId);

        // fallback for older installations
        if (!isset($projectSettings['email'])) {
            $projectSettings['email'] = '';
        }
        $this->view->settings           = $projectSettings;
        $this->view->projectId          = $projectId;
        $this->view->fallbackLanguageId = $languageModel->getFallbackLanguageId();

        // if user does not have edit rights just show the information
        if (!$this->_userModel->hasRight('editProject')) {
            $this->_helper->viewRenderer('view');
        }
    }

    /**
     * Process request and extract proper settings for current/default project
     *
     * @return array
     */
    protected function getProjectSettingsArray()
    {
        $projectId = $this->getProjectId();
        $projectSettings                              = $this->getProjectConfig($projectId);
        $projectSettings['name']                      = $this->_request->getParam('name');
        $projectSettings['url']                       = $this->_request->getParam('url');
        $projectSettings['email']                     = $this->_request->getParam('email');
        $projectSettings['logo_large']                = $this->_request->getParam('logo_large');
        $projectSettings['forceFallbackAsReference']  = $this->_request->getParam('forceFallbackAsReference', 0);
        $projectSettings['translateToFallback']       = $this->_request->getParam('translateToFallback', 0);
        $projectSettings['showStartPageWithoutLogin'] = $this->_request->getParam('showStartPageWithoutLogin', 0);
        return $projectSettings;
    }


    /**
     * Get project id from request parameters
     *
     * @return string
     */
    protected function getProjectId()
    {
        $projectId = $this->_request->get('project');
        if ($projectId === null) {
            return self::DEFAULT_PROJECT_ID;
        }

        return $projectId;
    }


    /**
     * Get config values for specified project
     *
     * @param string $projectId
     * @return mixed
     */
    protected function getProjectConfig($projectId)
    {
        return $this->_config->getParam('project')[$projectId];
    }


    /**
     * Get config values for all projects
     *
     * @return mixed
     */
    protected function getAllProjectsConfig()
    {
        return $this->_config->getParam('project');
    }
}

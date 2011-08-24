<?php
require_once 'AdminController.php';

class Admin_ProjectController extends AdminController
{
    public function indexAction()
    {
        /**
         * @var Zend_Controller_Request_Http $request
         */
        $request = $this->_request;
        if ($request->isPost()) {
            $projectSettings = $this->_config->getParam('project');
            $projectSettings['name'] = $this->_request->getParam('name');
            $projectSettings['url'] = $this->_request->getParam('url');
            $projectSettings['logo'] = $this->_request->getParam('logo');
            $this->_config->setParam('project', $projectSettings);
            $this->_config->save();
        }
        $this->view->settings = $this->_config->getParam('project');
    }
}

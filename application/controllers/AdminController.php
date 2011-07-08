<?php
class AdminController extends Zend_Controller_Action
{
    /**
     * Configuration object
     * @var Msd_Configuration
     */
    protected $_config;

    /**
     * Languages model
     * @var Application_Model_Languages
     */
    protected $_languagesModel;

    /**
     * User model
     * @var Application_Model_User
     */
    protected $_userModel;

    /**
     * Request object
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Init
     * Automatically read post and set session params
     *
     * @return void
     */
    public function init()
    {
        $this->_config = Msd_Configuration::getInstance();
        $this->_userModel = new Application_Model_User();
        // security - if user doesn't have admin rights -> send him to index page
        if (!$this->_userModel->hasRight('admin')) {
            $this->_redirect('/');
        }

        $this->_languagesModel = new Application_Model_Languages();
        $this->_request = $this->getRequest();
        if (!$this->getRequest()->isPost()) {
            $this->_setSessionParams();
        }
        $this->_getPostParams();
        $this->_assignVars();
        $this->view->languages = $this->_languagesModel->getLanguages();
    }

    /**
     * Index action forwards to users action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('index', 'admin_users');
    }

    /**
     * Get post params and set to config which is saved top session
     *
     * @return void
     */
    private function _getPostParams()
    {
        $filter = trim($this->_request->getParam('filterUser', ''));
        $offset = (int) $this->_request->getParam('offset', 0);
        $recordsPerPage = (int) $this->_config->get('dynamic.recordsPerPage');
        $recordsPerPage = (int) $this->_request->getParam('recordsPerPage', $recordsPerPage);
        $this->_config->set('dynamic.recordsPerPage', $recordsPerPage);
        $this->_config->set('dynamic.offset', $offset);
        $this->_config->set('dynamic.filterUser', $filter);
    }

    /**
     * Set default session values on first page call
     *
     * @return void
     */
    private function _setSessionParams()
    {
        // set defaults on first page call
        $this->_config->set('dynamic.offset', 0);
        $this->_config->set('dynamic.filterUser', '');
        $recordsPerPage = $this->_userModel->getUserRights('recordsPerPage');
        $this->_config->set('dynamic.recordsPerPage', $recordsPerPage);
    }

    /**
     * Assign params to view (formerly taken from post or session)
     *
     * @return void
     */
    private function _assignVars()
    {
        $this->view->filterUser     = $this->_config->get('dynamic.filterUser');
        $this->view->offset         = $this->_config->get('dynamic.offset');
        $this->view->recordsPerPage = $this->_config->get('dynamic.recordsPerPage');
        $this->view->languages      = $this->_languagesModel->getLanguages();
    }

}
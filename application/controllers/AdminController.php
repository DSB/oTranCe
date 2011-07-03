<?php
class AdminController extends Zend_Controller_Action
{
    /**
     * Configuration object
     * @var Msd_Configuration
     */
    private $_config;

    /**
     * Languages model
     * @var Application_Model_Languages
     */
    private $_languagesModel;

    /**
     * User model
     * @var Application_Model_User
     */
    private $_userModel;

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
        $this->_languagesModel = new Application_Model_Languages();
        $this->_request = $this->getRequest();
        if (!$this->getRequest()->isPost()) {
            $this->_setSessionParams();
        }
        $this->_getPostParams();
        $this->_assignVars();
    }

    /**
     * Index action forwards to users action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('users');
    }

    /**
     * Users action for maintaining users
     *
     * @return void
     */
    public function usersAction()
    {
        $params = $this->_request->getParams();
        if (isset($params['edit'])) {
            echo "Jo edit: " . $params['edit'];
        }

        $recordsPerPage = (int) $this->_config->get('dynamic.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->users = $this->_userModel->getUsers(
            $this->_config->get('dynamic.filterUser'),
            $this->_config->get('dynamic.offset'),
            $this->_config->get('dynamic.recordsPerPage')
        );
        $this->view->hits = $this->_userModel->getRowCount();
        $this->view->userModel = $this->_userModel;
    }

    /**
     * Languages action for maintaining languages
     *
     * @return void
     */
    public function languagesAction()
    {
    }

    /**
     * Files action for maintaining files
     *
     * @return void
     */
    public function filesAction()
    {
    }

    /**
     * Import action handles setting for imports
     *
     * @return void
     */
    public function importAction()
    {
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
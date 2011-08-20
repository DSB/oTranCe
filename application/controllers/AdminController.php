<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class AdminController extends Msd_Controller_Action
{
    /**
     * Languages model
     * @var Application_Model_LanguageEntries
     */
    protected $_languageEntriesModel;

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
     * Init
     * Automatically read post and set session params
     *
     * @return void
     */
    public function init()
    {
        $this->_userModel = new Application_Model_User();
        // security - if user doesn't have admin rights -> send him to index page
        if (!$this->_userModel->hasRight('admin')) {
            $this->_redirect('/');
        }

        $this->_languageEntriesModel = new Application_Model_LanguageEntries();
        $this->_languagesModel = new Application_Model_Languages();
        if (!$this->_request->isPost()) {
            $this->_setSessionParams();
        }
        $this->_getPostParams();
        $this->_assignVars();
        $this->view->languages = $this->_languagesModel->getAllLanguages('', 0, 0, false);
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
    protected function _getPostParams()
    {
        $filter = trim($this->_request->getParam('filterUser', ''));
        $offset = (int) $this->_request->getParam('offset', 0);
        $recordsPerPage = (int) $this->_dynamicConfig->getParam('recordsPerPage');
        $recordsPerPage = (int) $this->_request->getParam('recordsPerPage', $recordsPerPage);
        $this->_dynamicConfig->setParam('recordsPerPage', $recordsPerPage);
        $this->_dynamicConfig->setParam('offset', $offset);
        $this->_dynamicConfig->setParam('filterUser', $filter);
    }

    /**
     * Set default session values on first page call
     *
     * @return void
     */
    private function _setSessionParams()
    {
        // set defaults on first page call
        $this->_dynamicConfig->setParam('offset', 0);
        $this->_dynamicConfig->setParam('filterUser', '');
        $recordsPerPage = $this->_userModel->getUserRights('recordsPerPage');
        $this->_dynamicConfig->setParam('recordsPerPage', $recordsPerPage);
    }

    /**
     * Assign params to view (formerly taken from post or session)
     *
     * @return void
     */
    private function _assignVars()
    {
        $this->view->filterUser     = $this->_dynamicConfig->getParam('filterUser');
        $this->view->offset         = $this->_dynamicConfig->getParam('offset');
        $this->view->recordsPerPage = $this->_dynamicConfig->getParam('recordsPerPage');
        $this->view->languages      = $this->_languagesModel->getAllLanguages();
    }

}
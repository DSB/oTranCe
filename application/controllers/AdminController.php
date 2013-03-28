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
class AdminController extends OtranceController
{
    /**
     * Languages model
     *
     * @var \Application_Model_LanguageEntries
     */
    protected $_languageEntriesModel;

    /**
     * Languages model
     *
     * @var \Application_Model_Languages
     */
    protected $_languagesModel;

    /**
     * Name of the requested controller.
     *
     * @var string
     */
    protected $_requestedController;

    /**
     * Check general access right
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        $this->checkRight('admin');
    }

    /**
     * Init
     * Automatically read post and set session params
     *
     * @return void
     */
    public function init()
    {
        $this->_requestedController  = $this->_request->getControllerName();
        $this->_languageEntriesModel = new Application_Model_LanguageEntries();
        $this->_languagesModel       = new Application_Model_Languages();
        if ($this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage', null) == null) {
            $this->_setSessionParams();
        }
        $this->_getParams();
        $this->_assignVars();
        $this->view->languages = $this->_languagesModel->getAllLanguages('', 0, 0, false);
        $this->view->user      = $this->_userModel;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * Get post params and set to config which is saved top session
     *
     * @return void
     */
    protected function _getParams()
    {
        $recordsPerPage = (int)$this->_request->getParam(
            'recordsPerPage',
            (int)$this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage')
        );
        $this->_dynamicConfig->setParam($this->_requestedController . '.recordsPerPage', $recordsPerPage);

        $offset = (int)$this->_request->getParam('offset', 0);
        $this->_dynamicConfig->setParam($this->_requestedController . '.offset', $offset);

        $filterUser = trim($this->_request->getParam('filterUser', ''));
        $this->_dynamicConfig->setParam($this->_requestedController . '.filterUser', $filterUser);

        $filterLanguage = trim($this->_request->getParam('filterLanguage', ''));
        $this->_dynamicConfig->setParam($this->_requestedController . '.filterLanguage', $filterLanguage);
    }

    /**
     * Set default session values on first page call
     *
     * @return void
     */
    protected function _setSessionParams()
    {
        // set defaults on first page call
        $this->_dynamicConfig->setParam('adminInitiated', true);
        $this->_dynamicConfig->setParam($this->_requestedController . '.offset', 0);
        $this->_dynamicConfig->setParam($this->_requestedController . '.filterUser', '');
        $this->_dynamicConfig->setParam($this->_requestedController . '.filterLanguage', '');
        $this->_dynamicConfig->setParam(
            $this->_requestedController . '.recordsPerPage',
            $this->_userModel->loadSetting('recordsPerPage', 20)
        );
    }

    /**
     * Assign params to view (formerly taken from post or session)
     *
     * @return void
     */
    private function _assignVars()
    {
        $this->view->filterUser     = (string)$this->_dynamicConfig->getParam(
            $this->_requestedController . '.filterUser'
        );
        $this->view->filterLanguage = (string)$this->_dynamicConfig->getParam(
            $this->_requestedController . '.filterLanguage'
        );
        $this->view->offset         = (int)$this->_dynamicConfig->getParam($this->_requestedController . '.offset');
        $this->view->recordsPerPage = (int)$this->_dynamicConfig->getParam(
            $this->_requestedController . '.recordsPerPage'
        );
        $this->view->languages      = $this->_languagesModel->getAllLanguages();
    }

}

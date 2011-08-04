<?php
class LogController extends Zend_Controller_Action
{

    /**
     * User model
     * @var Application_Model_User
     */
    private $_userModel;

    /**
     * History model
     * @var Application_Model_History
     */
    private $_historyModel;

    /**
     * Languages model
     * @var Application_Model_LanguagesEntries
     */
    private $_entriesModel;

    /**
     * @var Application_Model_Languages
     */
    private $_languagesModel;

    /**
     * Init
     */
     public function init()
     {
         $this->_userModel      = new Application_Model_User();
         $this->_historyModel   = new Application_Model_History();
         $this->_entriesModel   = new Application_Model_LanguageEntries();
         $this->_languagesModel = new Application_Model_Languages();
     }

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $config = Msd_Configuration::getInstance();
        $recordsPerPage = $config->get('dynamic.recordsPerPage');
        if ($recordsPerPage < 10) {
            $recordsPerPage = 20;
        }
        $request        = $this->getRequest();
        $filterLanguage = $request->getParam('filterLanguage', '');
        $languages      = $this->_languagesModel->getAllLanguages();
        asort($languages);
        $this->view->selectFilterLanguage = Msd_Html::getHtmlOptionsFromAssocArray(
            $languages,
            'id',
            '{name} ({locale})',
            $filterLanguage,
            true
        );

        $filterUser = $request->getParam('filterUser', '');
        $users      = $this->_userModel->getUserNames();
        $this->view->selectFilterUser = Msd_Html::getHtmlOptions($users, $filterUser, true);

        $filterAction = $request->getParam('filterAction', '');
        $actions = array(
            'changed' => 'changed',
            'deleted %' => 'deleted',
            'created' => 'created',
            'updated SVN' => 'updated SVN',
            'logged in' => 'logged in',
            '%failed to log in' => 'log in failed',
            'logged out' => 'log out'
        );
        natcasesort($actions);
        $this->view->selectFilterAction = Msd_Html::getHtmlOptions($actions, $filterAction, true);

        $offset = $request->getParam('offset', 0);
        $this->view->logEntries = $this->_historyModel->getEntries(
            $offset,
            $recordsPerPage,
            $filterLanguage,
            $filterUser,
            $filterAction
        );
        $this->view->offset         = $offset;
        $this->view->recordsPerPage = $recordsPerPage;
        $this->view->languages      = $languages;
        $this->view->rows           = $this->_historyModel->getRowCount();
        $this->view->canDelete      = $this->_userModel->hasRight('addVar');
    }

    /**
     * Delete an entry of the history
     *
     * @return
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $historyModel = new Application_Model_History();
        $historyModel->deleteById($id);
        $this->_forward('index');
        return;
    }
}


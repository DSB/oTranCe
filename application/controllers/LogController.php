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
 * Log Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class LogController extends OtranceController
{
    /**
     * History model
     *
     * @var Application_Model_History
     */
    private $_historyModel;

    /**
     * Languages model
     *
     * @var Application_Model_LanguageEntries
     */
    private $_entriesModel;

    /**
     * @var Application_Model_Languages
     */
    private $_languagesModel;

    /**
     * Check general access right
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        $this->checkRight('showLog');
    }

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
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
        $recordsPerPage = (int)$this->_request->getParam(
            'recordsPerPage',
            $this->_dynamicConfig->getParam('log.recordsPerPage', 0)
        );
        if ($recordsPerPage == 0) {
            $recordsPerPage = $this->_userModel->loadSetting('recordsPerPage', 20);
        }
        $this->_dynamicConfig->setParam('log.recordsPerPage', $recordsPerPage);

        $filterLanguage = $this->_request->getParam('filterLanguage', '');
        $languages      = $this->_languagesModel->getAllLanguages();
        asort($languages);
        $this->view->selectFilterLanguage = Msd_Html::getHtmlOptionsFromAssocArray(
            $languages,
            'id',
            '{name} ({locale})',
            $filterLanguage,
            true
        );

        $filterUser                   = $this->_request->getParam('filterUser', '');
        $users                        = $this->_userModel->getUserNames();
        $this->view->selectFilterUser = Msd_Html::getHtmlOptions($users, $filterUser, true);

        $filterAction = $this->_request->getParam('filterAction', '');
        $translator   = $this->view->lang;
        $actions      = array(
            'changed'                   => $translator->translate('L_EDITED'),
            'deleted %'                 => 'Deleted key',
            'created'                   => 'Created key',
            'updated VCS'               => 'Updated VCS',
            'updated language packages' => 'Updated language packages',
            'logged in'                 => 'Logged in',
            '%failed to log in'         => 'Log in failed',
            'logged out'                => 'Logged out',
            'registered'                => 'Registered',
            'account approved %'        => 'Account approved',
            'account closed %'          => 'Account closed',
        );
        natcasesort($actions);
        $this->view->selectFilterAction = Msd_Html::getHtmlOptions($actions, $filterAction, true);

        $offset                        = $this->_request->getParam('offset', 0);
        $this->view->logEntries        = $this->_historyModel->getEntries(
            $offset,
            $recordsPerPage,
            $filterLanguage,
            $filterUser,
            $filterAction
        );
        $this->view->offset            = $offset;
        $this->view->recordsPerPage    = $recordsPerPage;
        $this->view->languages         = $languages;
        $this->view->rows              = $this->_historyModel->getRowCount();
        $this->view->canDelete         = $this->_userModel->hasRight('admin');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int)$this->view->recordsPerPage);
    }

    /**
     * Delete an entry of the history
     *
     * @return void
     */
    public function deleteAction()
    {
        if ($this->checkRight('admin')) {
            $id           = $this->getRequest()->getParam('id');
            $historyModel = new Application_Model_History();
            $historyModel->deleteById($id);
            $this->_forward('index');
        }

        return;
    }
}


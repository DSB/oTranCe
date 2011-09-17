<?php
require_once 'AdminController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Files Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class Admin_FilesController extends AdminController
{
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        if (!$this->_userModel->hasRight('editTemplate')) {
            $this->_redirect('/');
        }
    }

    /**
     * Retrieve data for index view.
     *
     * @return void
     */
    public function indexAction()
    {
        $templateOrderFields = array(
            'name' => 'Name',
            'filename' => 'Filename',
        );
        $templateOrderField = $this->_dynamicConfig->getParam($this->_requestedController . '.templateOrderField');
        if ($templateOrderField === null) {
            $templateOrderField = 'name';
        }
        $templatesModel = new Application_Model_FileTemplates();
        $recordsPerPage = (int) $this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->fileTemplates = $templatesModel->getFileTemplates(
            $templateOrderField,
            $this->_dynamicConfig->getParam($this->_requestedController . '.filterUser'),
            $this->_dynamicConfig->getParam($this->_requestedController . '.offset'),
            $this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage')
        );
        $this->view->hits = $templatesModel->getRowCount();
        $this->view->selOrderField = $templateOrderField;
        $this->view->templateOrderFields = $templateOrderFields;
    }

    /**
     * Get post params and set to config which is saved top session
     *
     * @return void
     */
    protected function _getPostParams()
    {
        $templateOrderField = $this->_dynamicConfig->getParam($this->_requestedController . '.templateOrderField');
        $templateOrderField = $this->_request->getParam('templateOrderBy', $templateOrderField);
        $this->_dynamicConfig->setParam($this->_requestedController . '.templateOrderField', $templateOrderField);
        parent::_getPostParams();
    }

    /**
     * Edit action for maintaining file templates
     *
     * @return void
     */
    public function editAction()
    {
        $templateId = $this->_request->getParam('id', 0);
        $templatesModel = new Application_Model_FileTemplates();
        // check if this is a new template and if the user is allowed to add it
        $template = $templatesModel->getFileTemplate($templateId);
        if ($template['id'] == 0) {
            if (!$this->_userModel->hasRight('addTemplate')) {
                $this->_redirect('/');
            }
        }

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $this->view->creationResult = $templatesModel->saveFileTemplate(
                $templateId,
                $params['tplName'],
                $params['tplHeader'],
                $params['tplContent'],
                $params['tplFooter'],
                $params['tplFile']
            );
        }
        $this->view->fileTemplate = $templatesModel->getFileTemplate($templateId);
    }

    /**
     * Deletes a file template, passes the result to the view script and invokes an internal forward to index action.
     *
     * @return void
     */
    public function deleteAction()
    {
        if ($this->_request->isPost()) {
            $templatesModel = new Application_Model_FileTemplates();
            $delTemplateId = $this->_request->getParam('delTemplateId', 0);
            $replacementId = $this->_request->getParam('replacementId', 0);
            $this->view->deletionResult = $templatesModel->deleteFileTemplate($delTemplateId, $replacementId);
        }
        $this->_forward('index');
    }
}

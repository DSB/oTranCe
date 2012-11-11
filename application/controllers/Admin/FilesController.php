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
 * Admin/Files Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
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
            'name'     => 'Name',
            'filename' => 'Filename',
        );
        $templateOrderField  = $this->_dynamicConfig->getParam($this->_requestedController . '.templateOrderField');
        if ($templateOrderField === null) {
            $templateOrderField = 'name';
        }
        $templatesModel = new Application_Model_FileTemplates();
        if ($this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage', null) == null) {
            $this->_setSessionParams();
        }
        $recordsPerPage                  = (int)$this->_dynamicConfig->getParam(
            $this->_requestedController . '.recordsPerPage'
        );
        $this->view->selRecordsPerPage   = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->fileTemplates       = $templatesModel->getFileTemplates(
            $templateOrderField,
            $this->_dynamicConfig->getParam($this->_requestedController . '.filterUser'),
            $this->_dynamicConfig->getParam($this->_requestedController . '.offset'),
            $this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage')
        );
        $this->view->hits                = $templatesModel->getRowCount();
        $this->view->selOrderField       = $templateOrderField;
        $this->view->templateOrderFields = $templateOrderFields;
    }

    /**
     * Get post params and set to config which is saved top session
     *
     * @return void
     */
    protected function _getParams()
    {
        $templateOrderField = $this->_dynamicConfig->getParam($this->_requestedController . '.templateOrderField');
        $templateOrderField = $this->_request->getParam('templateOrderBy', $templateOrderField);
        $this->_dynamicConfig->setParam($this->_requestedController . '.templateOrderField', $templateOrderField);
        parent::_getParams();
    }

    /**
     * Edit action for maintaining file templates
     *
     * @return void
     */
    public function editAction()
    {
        $this->view->errors = array();
        $templateId         = $this->_request->getParam('id', 0);
        $templatesModel     = new Application_Model_FileTemplates();

        // check if this is a new template and if the user is allowed to add it
        $template = $templatesModel->getFileTemplate($templateId);
        if ($template['id'] == 0) {
            if (!$this->_userModel->hasRight('addTemplate')) {
                $this->_redirect('/');
            }
        }

        if ($this->_request->isPost()) {
            $params     = $this->_request->getParams();
            $translator = Msd_Language::getInstance();
            if ($templatesModel->validateData($params, $translator)) {
                $res                        = $templatesModel->saveFileTemplate(
                    $templateId,
                    $params['name'],
                    $params['header'],
                    $params['content'],
                    $params['footer'],
                    $params['filename']
                );
                $this->view->creationResult = (bool)$res;
                if ($res !== false) {
                    $templateId = $res;
                }
            } else {
                $this->view->errors = $templatesModel->getValidateMessages();
            }
            $params['id']             = $templateId;
            $this->view->fileTemplate = $params;
        } else {
            $this->view->fileTemplate = $templatesModel->getFileTemplate($templateId);
        }
    }

    /**
     * Deletes a file template, passes the result to the view script and invokes an internal forward to index action.
     *
     * @return void
     */
    public function deleteAction()
    {
        if (!$this->_userModel->hasRight('addTemplate')) {
            $this->_redirect('/');
        }

        if ($this->_request->isPost()) {
            $templatesModel             = new Application_Model_FileTemplates();
            $delTemplateId              = $this->_request->getParam('delTemplateId', 0);
            $replacementId              = $this->_request->getParam('replacementId', 0);
            $this->view->deletionResult = $templatesModel->deleteFileTemplate($delTemplateId, $replacementId);
            //trigger ajax call to optimize database tables
            $this->_dynamicConfig->setParam('optimizeTables', true);
        }
        $this->_forward('index');
    }

    /**
     * Clone an existing file template
     *
     * @return void
     */
    public function cloneAction()
    {
        if (!$this->_userModel->hasRight('addTemplate')) {
            $this->_redirect('/');
        }
        $id                       = $this->_getParam('id');
        $templatesModel           = new Application_Model_FileTemplates();
        $template                 = $templatesModel->getFileTemplate($id);
        $template['id']           = 0;
        $this->view->fileTemplate = $template;
        $this->render('edit');
    }
}

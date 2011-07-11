<?php
require_once 'AdminController.php';
class Admin_FilesController extends AdminController
{
    public function indexAction()
    {
        $templateOrderFields = array(
            'name' => 'Name',
            'filename' => 'Filename',
        );
        $templateOrderField = $this->_config->get('dynamic.templateOrderField');
        if ($templateOrderField === null) {
            $templateOrderField = 'name';
        }
        $templatesModel = new Application_Model_FileTemplates();
        $recordsPerPage = (int) $this->_config->get('dynamic.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->fileTemplates = $templatesModel->getFileTemplates(
            $templateOrderField,
            $this->_config->get('dynamic.filterUser'),
            $this->_config->get('dynamic.offset'),
            $this->_config->get('dynamic.recordsPerPage')
        );
        $this->view->hits = $templatesModel->getRowCount();
        $this->view->selOrderField = $templateOrderField;
        $this->view->templateOrderFields = $templateOrderFields;
    }

    protected function _getPostParams()
    {
        $templateOrderField = $this->_config->get('dynamic.templateOrderField');
        $templateOrderField = $this->_getParam('templateOrderBy', $templateOrderField);
        $this->_config->set('dynamic.templateOrderField', $templateOrderField);
        parent::_getPostParams();
    }

    public function editAction()
    {
    }
}

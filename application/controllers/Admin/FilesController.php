<?php
require_once 'AdminController.php';
class Admin_FilesController extends AdminController
{
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

    /**
     * Get post params and set to config which is saved top session
     *
     * @return void
     */
    protected function _getPostParams()
    {
        $templateOrderField = $this->_config->get('dynamic.templateOrderField');
        $templateOrderField = $this->_getParam('templateOrderBy', $templateOrderField);
        $this->_config->set('dynamic.templateOrderField', $templateOrderField);
        parent::_getPostParams();
    }

    /**
     * Edit action for maintaining languages
     *
     * @return void
     */
    public function editAction()
    {
        $templateId = $this->_request->getParam('id', 0);
        $templatesModel = new Application_Model_FileTemplates();
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
}

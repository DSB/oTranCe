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
        $templatesModel = new Application_Model_FileTemplates();
        $recordsPerPage = (int) $this->_config->get('dynamic.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->languages = $templatesModel->getFileTemplates(
            $this->_config->get('dynamic.templateOrderField'),
            $this->_config->get('dynamic.filterUser'),
            $this->_config->get('dynamic.offset'),
            $this->_config->get('dynamic.recordsPerPage')
        );
        $this->view->hits = $templatesModel->getRowCount();

    }

}

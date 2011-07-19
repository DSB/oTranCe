<?php
class ImportController extends Zend_Controller_Action
{
    private $_languagesModel;
    private $_userModel;
    private $_config;
    protected $_request;
    private $_analyzerModel;
    private $_fileTemplatesModel;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_languagesModel = new Application_Model_LanguageEntries();
        $this->_userModel = new Application_Model_User();
        $this->_analyzerModel = new Application_Model_Analyzer();
        $this->_fileTemplatesModel = new Application_Model_FileTemplates();
        $this->_config = Msd_Configuration::getInstance();
        $this->_request = $this->getRequest();
    }

    /**
     * Handle index action
     *
     * @return void
     */
    public function indexAction()
    {
        $params = $this->_request->getParams();
        $selectedLanguage = (int) $this->_request->getParam('selectedLanguage', 0);
        $languages = $this->_languagesModel->getLanguages(true);
        $this->view->selLanguage = Msd_Html::getHtmlOptions($languages, $selectedLanguage);
        $this->view->selectedLanguage = $selectedLanguage;
        $this->view->importData = $this->_request->getParam('importData', '');
        if ($selectedLanguage != 0) {
            $languages = $this->_languagesModel->getLanguages();
            $selectedFileTemplate = (int) $this->_request->getParam('selectedFileTemplate', 0);
            $fileTemplates = array();
            $files = $this->_fileTemplatesModel->getFileTemplates('name');
            foreach ($files as $file) {
                $filename = str_replace('{LOCALE}', $languages[$selectedLanguage]['locale'], $file['filename']);
                $fileTemplates[$file['id']] = $filename;
            }
            $this->view->selFileTemplate = Msd_Html::getHtmlOptions($fileTemplates, $selectedFileTemplate);
        }

        $analyzers = $this->_analyzerModel->getAvailableImportAnalyzers();
        $analyzersNames = array_keys($analyzers);
        $selectedAnalyzer = $this->_request->getParam('selectedAnalyzer', $analyzersNames[0]);
        $this->view->selAnalyzer = Msd_Html::getHtmlOptions($analyzers, $selectedAnalyzer, count($analyzers) != 1);
        $this->view->selectedAnalyzer = $selectedAnalyzer;

        if ($this->_request->isPost()) {
            if (isset($_FILES['fileUploaded']) && $_FILES['fileUploaded']['size'] > 0) {
                $data = file_get_contents($_FILES['fileUploaded']['tmp_name']);
                $this->view->importData = trim($data);
            }
        }
        if (isset($params['analyze'])) {
            $this->_forward('analyze');
            return;
        }
    }

    /**
     * Analyze and extract detected constants from data
     *
     * @return void
     */
    public function analyzeAction()
    {
        $data = $this->view->importData;
        $importer = new Application_Model_Importer_Oxid();
        $this->view->extractedData = $importer->extract($data);
    }
}

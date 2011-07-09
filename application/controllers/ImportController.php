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
        $this->_languagesModel = new Application_Model_Languages();
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
        $request = $this->getRequest();
        $params = $request->getParams();
        $selectedLanguage = (int) $request->getParam('selectedLanguage', 0);
        $languages = $this->_languagesModel->getLanguages(true);
        $this->view->selLanguage = Msd_Html::getHtmlOptions($languages, $selectedLanguage);
        $this->view->selectedLanguage = $selectedLanguage;

        if ($selectedLanguage != 0) {
            $languages = $this->_languagesModel->getLanguages();
            $selectedFileTemplate = (int) $request->getParam('selectedFileTemplate', 0);
            $fileTemplates = array();
            $files = $this->_fileTemplatesModel->getFileTemplates('name');
            foreach ($files as $file) {
                $filename = str_replace('{LOCALE}', $languages[$selectedLanguage]['locale'], $file['filename']);
                $fileTemplates[$file['id']] = $filename;
            }
            $this->view->selFileTemplate = Msd_Html::getHtmlOptions($fileTemplates, $selectedFileTemplate);
        }

        $selectedAnalyzer = $request->getParam('selectedAnalyzer', '');
        $analyzers = $this->_analyzerModel->getAvailableImportAnalyzers();
        $this->view->selAnalyzer = Msd_Html::getHtmlOptions($analyzers, $selectedAnalyzer, count($analyzers) != 1);

        if (isset($params['analyze'])) {
            $this->_forward('analyze');
            return;
        }
    }

    public function analyzeAction()
    {
        $data = file_get_contents('../data/lang.php');
        $importer = new Application_Model_Importer_Oxid();
        $this->view->extractedData = $importer->extract($data);
    }
}

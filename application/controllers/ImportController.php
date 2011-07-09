<?php
class ImportController extends Zend_Controller_Action
{
    private $_languagesModel;
    private $_userModel;
    private $_config;
    protected $_request;
    private $_analyzerModel;

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

        $selectedLanguage = $request->getParam('selectedLanguage', '');
        $languages = $this->_languagesModel->getLanguages(true);
        $this->view->selLanguage = Msd_Html::getHtmlOptions($languages, $selectedLanguage);
        $this->view->selectedLanguage = $selectedLanguage;

        $selectedAnalyzer = $request->getParam('selectedAnalyzer', '');
        $analyzers = $this->_analyzerModel->getAvailableImportAnalyzers();
        $this->view->selAnalyzer = Msd_Html::getHtmlOptions($analyzers, $selectedAnalyzer);

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

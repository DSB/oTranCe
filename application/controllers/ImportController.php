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
 * Import Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class ImportController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_LanguageEntries
     */
    private $_entriesModel;
    /**
     * @var Application_Model_Languages
     */
    private $_languagesModel;
    /**
     * @var Application_Model_User
     */
    private $_userModel;
    /**
     * @var Msd_Configuration
     */
    private $_config;
    /**
     * @var Application_Model_Analyzer
     */
    private $_analyzerModel;
    /**
     * @var Application_Model_FileTemplates
     */
    private $_fileTemplatesModel;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_entriesModel = new Application_Model_LanguageEntries();
        $this->_languagesModel = new Application_Model_Languages();
        //TODO Get only languages the user is allowed to edit
        $this->_languages = $this->_languagesModel->getAllLanguages();
        $this->_userModel = new Application_Model_User();
        $this->_analyzerModel = new Application_Model_Analyzer();
        $this->_fileTemplatesModel = new Application_Model_FileTemplates();
        $this->_config = Msd_Configuration::getInstance();
    }

    /**
     * Handle index action
     *
     * @return void
     */
    public function indexAction()
    {
        $params = $this->_request->getParams();
        $selectedLanguage = $this->_getSelectedLanguage();

        $this->view->importData = $this->_request->getParam('importData', '');
        if ($selectedLanguage != 0) {
            $selectedFileTemplate = (int)$this->_request->getParam(
                'selectedFileTemplate',
                $this->_config->get('dynamic.importFileTemplate')
            );
            $this->_config->set('dynamic.importFileTemplate', $selectedFileTemplate);

            $fileTemplates = array();
            $files = $this->_fileTemplatesModel->getFileTemplates('name');
            foreach ($files as $file) {
                $filename = str_replace('{LOCALE}', $this->_languages[$selectedLanguage]['locale'], $file['filename']);
                $fileTemplates[$file['id']] = $filename;
            }
            $this->view->selFileTemplate = Msd_Html::getHtmlOptions($fileTemplates, $selectedFileTemplate, false);
        }

        $this->_setAnalyzer();
        $this->_setSelectedCharset();

        if ($this->_request->isPost()) {
            if (isset($_FILES['fileUploaded']) && $_FILES['fileUploaded']['size'] > 0) {
                $data = trim(file_get_contents($_FILES['fileUploaded']['tmp_name']));
                $this->_config->set('dynamic.importOriginalData', $data);
                $this->view->importData = $data;
            }
        }

        if (isset($params['convert'])) {
            $entriesModel = new Application_Model_Converter();
            $res = $entriesModel->convertData($this->_config->get('dynamic.selectedCharset'), $this->_config->get('dynamic.importOriginalData'));
            if ($res === false) {
                $res = $data;
                $this->view->conversionError = true;
                $this->view->targetCharset = $this->_config->get('dynamic.selectedCharset');
            }
            $this->_config->set('dynamic.importConvertedData', $res);
            $this->view->importData = $res;
        }
        if (isset($params['analyze'])) {
            $this->_config->set('dynamic.importConvertedData', $this->view->importData);
            $this->_forward('analyze');
            return;
        }
    }

    private function _getSelectedLanguage()
    {
        $selectedLanguage = (int)$this->_request->getParam(
            'selectedLanguage',
            $this->_config->get('dynamic.selectedLanguage')
        );
        $this->_config->set('dynamic.selectedLanguage', $selectedLanguage);
        $this->view->selectedLanguage = $selectedLanguage;

        $this->view->selLanguage = Msd_Html::getHtmlOptionsFromAssocArray(
            $this->_languages,
            'id',
            '{name} ({locale})',
            $selectedLanguage,
            false
        );
        return $selectedLanguage;
    }

    private function _setSelectedCharset()
    {
        if ($this->_config->get('dynamic.selectedCharset') == null) {
            $this->_config->set('dynamic.selectedCharset', 'utf8');
        }
        $selectedCharset = $this->_request->getParam('selectedCharset', $this->_config->get('dynamic.selectedCharset'));
        $this->_config->set('dynamic.selectedCharset', $selectedCharset);
        $this->_dbo = Msd_Db::getAdapter();
        $charactersets = $this->_dbo->getCharsets();
        $this->view->selCharset = Msd_Html::getHtmlOptionsFromAssocArray(
            $charactersets,
            'Charset',
            "{Charset} -\t {Description}",
            $selectedCharset,
            false
        );
        return $selectedCharset;
    }

    /**
     * Get and set used analyzer
     *
     * @return void
     */
    private function _setAnalyzer()
    {
        $analyzers = $this->_analyzerModel->getAvailableImportAnalyzers();
        $analyzersNames = array_keys($analyzers);
        if ($this->_config->get('dynamic.selectedAnalyzer') == null) {
            $this->_config->set('dynamic.selectedAnalyzer', $analyzersNames[0]);
        }
        $selectedAnalyzer = $this->_request->getParam(
            'selectedAnalyzer',
            $this->_config->get('dynamic.selectedAnalyzer')
        );
        $this->_config->set('dynamic.selectedAnalyzer', $selectedAnalyzer);
        $this->view->selAnalyzer = Msd_Html::getHtmlOptions($analyzers, $selectedAnalyzer, false);
        $this->view->selectedAnalyzer = $selectedAnalyzer;
    }

    /**
     * Analyze and extract detected constants from data
     *
     * @return void
     */
    public function analyzeAction()
    {
        $selectedAnalyzer =             $this->_config->get('dynamic.selectedAnalyzer');
        $data = $this->_config->get('dynamic.importConvertedData');
        $importer = 'Application_Model_Importer_' . $selectedAnalyzer;
        $importer = new $importer();
        $this->view->fileTemplate  = $this->_config->get('dynamic.importFileTemplate');
        $this->view->language      = $this->_config->get('dynamic.selectedLanguage');
        $this->view->extractedData = $importer->extract($data);
    }
}

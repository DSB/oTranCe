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
class ImportController extends OtranceController
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
     * @var Application_Model_FileTemplates
     */
    private $_fileTemplatesModel;

    /**
     * Ass. languages array which the user is allowed to edit
     *
     * @var array
     */
    private $_languages;

    /**
     * Check general access right
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        $this->checkRight('showImport');
    }

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_entriesModel       = new Application_Model_LanguageEntries();
        $this->_languagesModel     = new Application_Model_Languages();
        $this->_fileTemplatesModel = new Application_Model_FileTemplates();
        // build array containing those languages the user is allowed to edit
        $allLanguages  = $this->_languagesModel->getAllLanguages();
        $userLanguages = $this->_userModel->getUserLanguageRights();
        if (!empty($userLanguages)) {
            $userLanguages = array_flip($userLanguages);
        } else {
            $userLanguages = array();
        }
        $this->_languages = array_uintersect_assoc($allLanguages, $userLanguages, create_function(null, "return 0;"));
        // add module path for analyzers
        $path = realpath(APPLICATION_PATH . '/../modules/library/Import/views') . '/';
        $this->view->addScriptPath($path);
    }

    /**
     * Handle index action
     *
     * @return void
     */
    public function indexAction()
    {
        $params = $this->_request->getParams();
        $this->_getSelectedLanguage();
        $this->_setSelectedFileTemplate();
        $this->_setAnalyzer();
        $this->_setSelectedCharset();
        $this->view->importData = $this->_request->getParam('importData', '');

        if ($this->_request->isPost()) {
            if (isset($_FILES['fileUploaded']) && $_FILES['fileUploaded']['size'] > 0) {
                $data = trim(file_get_contents($_FILES['fileUploaded']['tmp_name']));
                $this->_dynamicConfig->setParam('importOriginalData', $data);
                $this->view->importData = $data;
            }
        }

        if (isset($params['convert'])) {
            $this->_convertTextInput();
        }
        if (isset($params['analyze'])) {
            $this->_dynamicConfig->setParam('importConvertedData', $this->view->importData);
            $this->_forward('analyze');

            return;
        }
    }

    /**
     * Converts the text from given charset to utf-8.
     *
     * @return void
     */
    protected function _convertTextInput()
    {
        $converterModel = new Application_Model_Converter();
        $res            = $converterModel->convertData(
            $this->_dynamicConfig->getParam('selectedCharset'),
            $this->_dynamicConfig->getParam('importOriginalData')
        );
        if ($res === false) {
            // we show the user our own error message instead of the mysql error
            // $this->view->convertError = $e->getMessage();
            $this->view->conversionError = true;
            $this->view->targetCharset   = $this->_dynamicConfig->getParam('selectedCharset');
            // re-assign unconverted data
            $this->view->importData = $this->_dynamicConfig->getParam('importOriginalData');

            return;
        }
        $this->_dynamicConfig->setParam('importConvertedData', $res);
        $this->view->importData = $res;
    }

    /**
     * Handle selected language and set selectbox in view
     *
     * @return void
     */
    private function _getSelectedLanguage()
    {
        $selectedLanguage = (int)$this->_request->getParam(
            'selectedLanguage',
            $this->_dynamicConfig->getParam('selectedLanguage')
        );

        if (!isset($this->_languages[$selectedLanguage])) {
            // user is not allowed to edit the given language
            $userLanguages    = $this->_userModel->getUserLanguageRights();
            $selectedLanguage = $userLanguages[0];
        }
        $this->_dynamicConfig->setParam('selectedLanguage', $selectedLanguage);
        $this->view->selectedLanguage = $selectedLanguage;
        $this->view->selLanguage      = Msd_Html::getHtmlOptionsFromAssocArray(
            $this->_languages,
            'id',
            '{name} ({locale})',
            $selectedLanguage,
            false
        );
    }

    /**
     * Handle selection of FileTemplate and set selectbox in view
     *
     * @return void
     */
    private function _setSelectedFileTemplate()
    {
        $selectedFileTemplate = (int)$this->_request->getParam(
            'selectedFileTemplate',
            $this->_dynamicConfig->getParam('importFileTemplate')
        );
        $this->_dynamicConfig->setParam('importFileTemplate', $selectedFileTemplate);
        $this->view->selectedFileTemplate = $selectedFileTemplate;
    }

    /**
     * Handle selected charset for import and set selectbox in view
     *
     * @return void
     */
    private function _setSelectedCharset()
    {
        if ($this->_dynamicConfig->getParam('selectedCharset') == null) {
            $this->_dynamicConfig->setParam('selectedCharset', 'utf8');
        }
        $selectedCharset = $this->_request->getParam(
            'selectedCharset',
            $this->_dynamicConfig->getParam('selectedCharset')
        );
        $this->_dynamicConfig->setParam('selectedCharset', $selectedCharset);
        $this->_dbo             = Msd_Db::getAdapter();
        $characterSets          = $this->_dbo->getCharsets();
        $this->view->selCharset = Msd_Html::getHtmlOptionsFromAssocArray(
            $characterSets,
            'Charset',
            "{Charset} -\t {Description}",
            $selectedCharset,
            false
        );
    }

    /**
     * Handle selected analyzer and set selectbox in view
     *
     * @return void
     */
    private function _setAnalyzer()
    {
        $importerModel      = new Application_Model_Importers();
        $availableImporters = $importerModel->getActiveImporters();
        $importers          = array();
        foreach ($availableImporters as $importerName => $status) {
            $importers[$importerName] = $importerName;
        }
        // get Importer from session. If not set get standard importer.
        $selectedImporter = $this->_dynamicConfig->getParam('selectedAnalyzer', $importerModel->getStandardImporter());

        // if sent via form
        if ($this->_request->getParam('selectedAnalyzer', false) !== false) {
            $selectedImporter = $this->_request->getParam('selectedAnalyzer');
        }

        if (!isset($importers[$selectedImporter])) {
            $importerNames = array_keys($availableImporters);
            if (isset($importerNames[0])) {
                $selectedImporter = $importerNames[0];
            }
        }

        $this->_dynamicConfig->setParam('selectedAnalyzer', $selectedImporter);
        $this->view->analyzers        = $importers;
        $this->view->selAnalyzer      = Msd_Html::getHtmlOptions($importers, $selectedImporter, false);
        $this->view->selectedAnalyzer = $selectedImporter;
        try {
            $importer = Msd_Import::factory($selectedImporter);
        } catch (Exception $e) {
            $importer = null;
        }
        $this->view->analyzer = $importer;
    }

    /**
     * Analyze and extract detected constants from data
     *
     * @return void
     */
    public function analyzeAction()
    {
        // clear session log
        $sessionLog = new Msd_SessionLog('importLog');
        $sessionLog->clear();

        $selectedAnalyzer         = $this->_dynamicConfig->getParam('selectedAnalyzer');
        $data                     = $this->_dynamicConfig->getParam('importConvertedData');
        $importer                 = Msd_Import::factory($selectedAnalyzer);
        $this->view->fileTemplate = $this->_dynamicConfig->getParam('importFileTemplate');
        $this->view->language     = $this->_dynamicConfig->getParam('selectedLanguage');
        $extractedData            = $importer->extract($data);
        $extractedData            = array_map('stripslashes', $extractedData);
        $this->_dynamicConfig->setParam('importOriginalData', null);
        $this->_dynamicConfig->setParam('importConvertedData', null);
        $this->_dynamicConfig->setParam('extractedData', $extractedData);
        $this->view->extractedData = $extractedData;
    }

    /**
     * Show results after finishing import
     *
     * @return void
     */
    public function resultAction()
    {
        $sessionLog = new Msd_SessionLog('importLog');
        $this->view->assign(
            array(
                'success'        => $sessionLog->getMessagesOfType(Msd_SessionLog::TYPE_SUCCESS),
                'warning'        => $sessionLog->getMessagesOfType(Msd_SessionLog::TYPE_WARNING),
                'error'          => $sessionLog->getMessagesOfType(Msd_SessionLog::TYPE_ERROR),
                'importedValues' => $this->_dynamicConfig->getParam('extractedData'),
            )
        );
    }
}

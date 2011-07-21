<?php
class ExportController extends Zend_Controller_Action
{

    /**
     * @var Application_Model_LanguageEntries
     */
    private $_languageEntriesModel;
    /**
     * @var Application_Model_History
     */
    private $_historyModel;
    /**
     * @var Msd_Export
     */
    private $_export;
    /**
     * @var Application_Model_Languages
     */
    private $_languagesModel;

    public function init()
    {
        $this->_languageEntriesModel = new Application_Model_LanguageEntries();
        $this->_languagesModel = new Application_Model_Languages();
        $this->_export = new Msd_Export();
        $this->_historyModel = new Application_Model_History();
    }

    public function indexAction()
    {
        $this->view->status = $this->_languageEntriesModel->getStatus();
        $this->view->languages = $this->_languageEntriesModel->getLanguages();
        $this->view->historyModel = $this->_historyModel;
        $this->view->export = $this->_export;
    }

    /**
     * Update a specific language pack
     *
     * Create the language file and upload it to svn repository.
     *
     * @return void
     */
    public function updateAction()
    {
        $request = $this->getRequest();
        $language = $request->getParam('language');
        $languageInfo = $this->_languagesModel->getLanguageById($language);
        $this->view->language = $languageInfo['locale'];
        $allLangs = array_keys($this->_languageEntriesModel->getLanguages());
        if (!in_array($language, $allLangs)) {
            // non existant language submitted; quietly return to index page
            $this->_forward('index');
            return;
        }
        $res = $this->_export->exportLanguageFile($language);
        $this->view->filesize = $res;
        if ($res) {
            $this->view->svnExportResult = $this->_export->updateSvn($language);
            $this->_historyModel->logSvnUpdate($language);
        }
    }

    /**
     * Update all language packs at once
     *
     * Create the language files and upload them to svn repository.
     *
     * @return void
     */
    public function updateAllAction()
    {
        $langs = $this->_languageEntriesModel->getLanguages();
        $languages = array();
        $i = 0;
        $exportError = false;
        foreach ($langs as $lang => $name) {
            $languages[$i] = array();
            $languages[$i]['key'] = $lang;
            $languages[$i]['name'] = $name;
            $languages[$i]['filesize'] = $this->_export->exportLanguageFile($lang);
            if ($languages[$i]['filesize'] === false) {
                $exportError = true;
            }
            $i++;
        }
        if ($exportError === false) {
            $this->view->svnExportResult = $this->_export->updateSvnAll($lang);
            $this->_historyModel->logSvnUpdateAll($lang);
        }
        $this->view->languages = $languages;
    }
}

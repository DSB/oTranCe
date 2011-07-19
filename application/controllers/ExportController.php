<?php
class ExportController extends Zend_Controller_Action
{

    private $_languageModel;
    private $_historyModel;
    private $_export;

    public function init()
    {
        $this->_languageModel = new Application_Model_LanguageEntries();
        $this->_export = new Msd_Export();
        $this->_historyModel = new Application_Model_History();
    }

    public function indexAction()
    {
        $this->view->status = $this->_languageModel->getStatus();
        $this->view->languages = $this->_languageModel->getLanguages();
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
        $this->view->language = $language;
        $allLangs = array_keys($this->_languageModel->getLanguages());
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
        $langs = $this->_languageModel->getLanguages();
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

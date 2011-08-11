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
 * Export Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
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

    /**
     * @var Msd_Vcs_Interface
     */
    private $_vcs = null;

    /**
     * @var Msd_Configuration
     */
    private $_config;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_languageEntriesModel = new Application_Model_LanguageEntries();
        $this->_languagesModel = new Application_Model_Languages();
        $this->_export = new Msd_Export();
        $this->_historyModel = new Application_Model_History();
        $this->_config = Msd_Configuration::getInstance();
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->languages = $this->_languagesModel->getAllLanguages();
        $this->view->status = $this->_languageEntriesModel->getStatus($this->view->languages);
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
        $vcs = $this->_getVcsInstance();
        $vcs->update();
        $language = $this->_request->getParam('language');
        if (!$this->_languageExists($language)) {
            // If the user provides an invalid language id, redirect to index action, silently.
            $this->_forward('index');
            return;
        }

        $languageInfo = $this->_languagesModel->getLanguageById($language);
        $this->view->language = $languageInfo;
        $exportedFiles = $this->_export->exportLanguageFile($language);
        $this->view->exportOk = $exportedFiles['exportOk'];
        unset($exportedFiles['exportOk']);
        $this->_writeExportLog($exportedFiles);
        $this->view->exportedFiles = $exportedFiles;
    }

    public function commitAction()
    {
        $vcs = $this->_getVcsInstance();
        $statusResult = $vcs->status();
        $log = new Application_Model_ExportLog();
        if (!empty($statusResult)) {
            $files = $log->getFileList(session_id());
            $files = $this->_getCommitFileList($statusResult, $files, $vcs);
            $commtResult = $vcs->commit($files, $this->_config->get('config.vcs.commitMessage','Languagepack update'));
            $this->view->commitResult = $commtResult;
        } else {
            $this->view->commitResult = array('stdout' => 'Nothing to do.');
        }
        $log->delete(session_id());
    }

    private function _getCommitFileList($statusResult, $files, $vcs)
    {
        if (isset($statusResult['unversioned'])) {
            // Compare unversioned file list with exported file list.
            $addFiles = array();
            foreach ($statusResult['unversioned'] as $unverFile) {
                foreach ($files as $file) {
                    if (substr($file, 0, strlen($unverFile)) == $unverFile) {
                        $addFiles[] = $unverFile;
                        $addFiles[] = dirname($file);
                        $addFiles[] = $file;
                    }
                }
            }
            $addFiles = array_unique($addFiles);
            $vcs->add($addFiles);
            foreach (array_reverse($addFiles) as $addFile) {
                array_unshift($files, $addFile);
            }
        }

        $files = array_unique($files);
        return $files;
    }

    /**
     * @return Msd_Vcs_Interface
     */
    private function _getVcsInstance()
    {
        // TODO: Make VCS configurable from GUI
        if ($this->_vcs === null) {
            $vcsConfig = $this->_config->get('config.vcs');
            $this->_vcs = Msd_Vcs::factory($vcsConfig['class'], $vcsConfig['options']);
        }

        return $this->_vcs;
    }

    private function _writeExportLog($exportedFiles)
    {
        $exportLog = new Application_Model_ExportLog();
        foreach ($exportedFiles as $exportedFile) {
            if ($exportedFile['size'] !== false) {
                $exportLog->add(session_id(), $exportedFile['filename']);
            }
        }
    }

    private function _languageExists($lang)
    {
        $allLangs = array_keys($this->_languagesModel->getAllLanguages());
        $langExists = false;
        if (in_array($lang, $allLangs)) {
            $langExists = true;
        }

        return $langExists;
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
        $this->_getVcsInstance()->update();
        $langs = $this->_languagesModel->getAllLanguages();
        $languages = array();
        $i = 0;
        $exportOk = true;
        foreach ($langs as $lang => $langMeta) {
            if ($langMeta['active'] == 0) {
                continue;
            }
            $languages[$i] = array();
            $languages[$i]['key'] = $lang;
            $languages[$i]['meta'] = $langMeta;
            $exportResult = $this->_export->exportLanguageFile($lang);
            $exportOk = $exportOk && $exportResult['exportOk'];
            unset($exportResult['exportOk']);
            $this->_writeExportLog($exportResult);
            $languages[$i]['files'] = $exportResult;
            $i++;
        }
        $this->view->exportOk = $exportOk;
        $this->view->languages = $languages;
    }

    public function svnAction()
    {
        var_dump(Msd_Vcs::getAvailableAdapter());
        die();
    }
}

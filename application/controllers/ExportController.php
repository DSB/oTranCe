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
class ExportController extends Msd_Controller_Action
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
        $this->_buildArchives($exportedFiles);
    }

    /**
     * Commit changes to VCS.
     *
     * @return void
     */
    public function commitAction()
    {
        $vcs = $this->_getVcsInstance();
        $statusResult = $vcs->status();
        $log = new Application_Model_ExportLog();
        if (!empty($statusResult)) {
            $files = $log->getFileList(session_id());
            $files = $this->_getCommitFileList($statusResult, $files, $vcs);
            $vcsConfig = $this->_config->getParam('vcs');
            $commitMessage = 'Language pack update';
            if (isset($vcsConfig['commitMessage'])) {
                $commitMessage = $vcsConfig['commitMessage'];
            }
            $commitResult = $vcs->commit($files, $commitMessage);
            $this->view->commitResult = $commitResult;
        } else {
            $this->view->commitResult = array('stdout' => 'Nothing to do.');
        }
        $log->delete(session_id());
    }

    /**
     * Builds the language pack archives.
     *
     * @return void
     */
    private function _buildArchives()
    {
        $fileTree = new Application_Model_FileTree(EXPORT_PATH);
        $fileList = $fileTree->getSimpleTree();
        $filename = DOWNLOAD_PATH . DS . 'language_pack-' . date('Ymd-His');
        $zipArch = Msd_Archive::factory('Zip', $filename, EXPORT_PATH);
        $tarGzArch = Msd_Archive::factory('Tar_Gz', $filename, EXPORT_PATH);
        $tarBz2Arch = Msd_Archive::factory('Tar_Bz2', $filename, EXPORT_PATH);
        foreach ($fileList as $file) {
            $zipArch->addFile($file);
            $tarGzArch->addFile($file);
            $tarBz2Arch->addFile($file);
        }
        $zipArch->buildArchive();
        $tarGzArch->buildArchive();
        $tarBz2Arch->buildArchive();
    }

    /**
     * Builds the file list for VCS commit.
     *
     * @param array $statusResult
     * @param array $files
     * @param Msd_Vcs_Interface $vcs
     *
     * @return array
     */
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
        if ($this->_vcs === null) {
            $vcsConfig = $this->_config->getParam('vcs');
            $userModel = new Application_Model_User();
            $cryptedVcsCreds = $userModel->loadSetting('vcsCredentials', null);
            if ($cryptedVcsCreds !== null) {
                $projectConfig = $this->_config->getParam('project');
                $msdCrypt = new Msd_Crypt($projectConfig['encryptionKey']);
                $vcsCredentials = $msdCrypt->decrypt($cryptedVcsCreds);
                $vcsCredFields = Msd_Vcs::getCredentialFields($vcsConfig['adapter']);
                list ($vcsUser, $vcsPass) = explode('%@%', $vcsCredentials);
                $vcsConfig[$vcsCredFields['username']] = $vcsUser;
                $vcsConfig[$vcsCredFields['password']] = $vcsPass;
            }
            $this->_vcs = Msd_Vcs::factory($vcsConfig['adapter'], $vcsConfig['options']);
        }

        return $this->_vcs;
    }

    /**
     * Adds an etry to the export log.
     *
     * @param array $exportedFiles
     *
     * @return void
     */
    private function _writeExportLog($exportedFiles)
    {
        $exportLog = new Application_Model_ExportLog();
        foreach ($exportedFiles as $exportedFile) {
            if ($exportedFile['size'] !== false) {
                $exportLog->add(session_id(), $exportedFile['filename']);
            }
        }
    }

    /**
     * Checks wheter the language exsists and is active.
     *
     * @param int $lang
     * 
     * @return bool
     */
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
            $this->_buildArchives($exportResult);
            $languages[$i]['files'] = $exportResult;
            $i++;
        }
        $this->view->exportOk = $exportOk;
        $this->view->languages = $languages;
    }
}

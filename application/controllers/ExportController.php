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
     * @var Application_Model_Export
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
     * Project config from configuration
     *
     * @var array
     */
    private $_projectConfig;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $userModel = new Application_Model_User();
        if (!$userModel->hasRight('showExport')) {
            $this->_redirect('/');
        }
        $this->view->user            = $userModel;
        $this->_languageEntriesModel = new Application_Model_LanguageEntries();
        $this->_languagesModel       = new Application_Model_Languages();
        $this->_export               = new Application_Model_Export();
        $this->_historyModel         = new Application_Model_History();
        $this->_projectConfig        = $this->_config->getParam('project');
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->languages    = $this->_languagesModel->getAllLanguages();
        $this->view->status       = $this->_languageEntriesModel->getStatus($this->view->languages);
        $this->view->historyModel = $this->_historyModel;
        $this->view->export       = $this->_export;
        $log                      = new Application_Model_ExportLog();
        $this->view->vcsActivated = (bool)($this->_projectConfig['vcsActivated'] && ($log->getExportsCount() > 0));
    }

    /**
     * Commit changes to VCS.
     *
     * @return void
     */
    public function commitAction()
    {
        $vcs = $this->_getVcsInstance();
        $vcs->update();
        $statusResult = $vcs->status();
        $log          = new Application_Model_ExportLog();
        if (!empty($statusResult)) {
            $files         = $log->getFileList(session_id());
            $files         = $this->_getCommitFileList($statusResult, $files, $vcs);
            $vcsConfig     = $this->_config->getParam('vcs');
            $commitMessage = 'Language pack update';
            if (isset($vcsConfig['commitMessage'])) {
                $commitMessage = $vcsConfig['commitMessage'];
            }
            $commitResult = $vcs->commit($files, $commitMessage);
            $historyModel = new Application_Model_History();
            $historyModel->logVcsUpdateAll();
            $this->view->commitResult = $commitResult;
        } else {
            $this->view->commitResult = array('stdout' => $this->view->lang->translate('L_NOTHING_TO_DO') . '.');
        }
        $log->delete(session_id());
    }

    /**
     * Builds the file list for VCS commit.
     *
     * @param array             $statusResult Array with status infos
     * @param array             $files        Filelist
     * @param Msd_Vcs_Interface $vcs          VCS-Adapter to use
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
     * Return vcs adapter instance depending on the project's settings
     *
     * @return Msd_Vcs_Interface
     */
    private function _getVcsInstance()
    {
        if ($this->_vcs === null) {
            $vcsConfig       = $this->_config->getParam('vcs');
            $userModel       = new Application_Model_User();
            $cryptedVcsCreds = $userModel->loadSetting('vcsCredentials', null);
            if ($cryptedVcsCreds !== null) {
                $projectConfig  = $this->_config->getParam('project');
                $msdCrypt       = new Msd_Crypt($projectConfig['encryptionKey']);
                $vcsCredentials = $msdCrypt->decrypt($cryptedVcsCreds);
                $vcsCredFields  = Msd_Vcs::getCredentialFields($vcsConfig['adapter']);
                if (strpos($vcsCredentials, '%@%') !== false) {
                    list ($vcsUser, $vcsPass) = explode('%@%', $vcsCredentials);
                    $vcsConfig['options'][$vcsCredFields['username']] = $vcsUser;
                    $vcsConfig['options'][$vcsCredFields['password']] = $vcsPass;
                }
            }
            $this->_vcs = Msd_Vcs::factory($vcsConfig['adapter'], $vcsConfig['options']);
        }

        return $this->_vcs;
    }

    /**
     * Adds an etry to the export log.
     *
     * @param array $exportedFiles List of exported files
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
     * Update all language packs at once
     *
     * Create the language files and upload them to svn repository.
     *
     * @return void
     */
    public function updateAllAction()
    {
        $this->_vcsUpdate();
        $langs         = $this->_languagesModel->getAllLanguages();
        $languages     = array();
        $i             = 0;
        $exportOk      = true;
        $exportedFiles = array();
        foreach ($langs as $lang => $langMeta) {
            if ($langMeta['active'] == 0) {
                continue;
            }
            $languages[$i]         = array();
            $languages[$i]['key']  = $lang;
            $languages[$i]['meta'] = $langMeta;
            $exportResult          = $this->_export->exportLanguageFile($lang);
            $exportOk              = $exportOk && $exportResult['exportOk'];
            unset($exportResult['exportOk']);
            foreach ($exportResult as $result) {
                $exportedFiles[] = $result['filename'];
            }
            $this->_writeExportLog($exportResult);
            $languages[$i]['files'] = $exportResult;
            $i++;
        }
        $fileExportModel              = new Application_Model_FileExport();
        $this->view->isArchiveCreated = $fileExportModel->buildArchives($exportedFiles);
        $this->view->exportOk         = $exportOk;
        $this->view->languages        = $languages;
    }

    /**
     * Perform vcs update action
     *
     * @return void
     */
    private function _vcsUpdate()
    {
        if ($this->_projectConfig['vcsActivated'] == 1) {
            $vcs = $this->_getVcsInstance();
            $vcs->update();
        }
    }

}

<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Models
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * File model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_FileExport extends Msd_Application_Model
{
    /**
     * Builds the language pack archives.
     *
     * @param array $fileList List of files that will be packed
     *
     * @return bool If at least one archive could be built successfully return true
     */
    public function buildArchives($fileList)
    {
        $this->_cleanDownloads();

        $fileName = DOWNLOAD_PATH . '/' . $this->getLanguagePackBaseFileName() . '_languagePack';

        $zipArch   = Msd_Archive::factory('Zip', $fileName, EXPORT_PATH);
        $tarGzArch = Msd_Archive::factory('Tar_Gz', $fileName, EXPORT_PATH);
        $tarBzArch = Msd_Archive::factory('Tar_Bz2', $fileName, EXPORT_PATH);
        foreach ($fileList as $file) {
            $zipArch->addFile($file);
            $tarGzArch->addFile($file);
            $tarBzArch->addFile($file);
        }

        $isArchiveCreated = false;
        $isArchiveCreated &= $tarGzArch->buildArchive();
        $isArchiveCreated &= $tarBzArch->buildArchive();
        try {
            $isArchiveCreated &= $zipArch->buildArchive();
        } catch (Msd_Archive_Zip_Exception $e) {
            $isArchiveCreated = false;
        }

        return $isArchiveCreated;
    }

    /**
     * Delete all files in DOWNLOAD_PATH
     *
     * @return void
     */
    private function _cleanDownloads()
    {
        $iterator = new DirectoryIterator(DOWNLOAD_PATH);
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            unlink(DOWNLOAD_PATH . '/' . $file->getFilename());
        }
    }

    /**
     * Build file name from project name
     *
     * @return string
     */
    public function getLanguagePackBaseFileName()
    {
        $projectInfo = $this->_config->getParam('project');
        $fileName    = preg_replace('/[^a-z0-9-]/i', '_', $projectInfo['name']);
        $fileName    = preg_replace('/__+/', '_', $fileName);

        return $fileName;
    }
}

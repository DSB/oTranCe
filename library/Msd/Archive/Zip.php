<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Archive
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Class for ZIP archive creation.
 *
 * @package         MySQLDumper
 * @subpackage      Archive
 */
class Msd_Archive_Zip extends Msd_Archive_Abstract
{
    /**
     * Complete path- and filename of the archive.
     *
     * @var string
     */
    private $_archiveFilename;

    /**
     * @var ZipArchive
     */
    private $_zip;

    /**
     * Checkes for PHP Zip extension. Creates an instance of ZipArchive, if extension is loaded.
     *
     * @throws Msd_Archive_Zip_Exception
     *
     * @return void
     */
    public function init()
    {
        if (!extension_loaded('zip')) {
            throw new Msd_Archive_Zip_Exception(
                "The ZIP extension is not loaded. You need to install/enable the extension to use this archive class."
            );
        }
        $this->_archiveFilename = $this->_baseArchiveName . '.zip';
        $this->_zip = new ZipArchive();
    }

    /**
     * Creates the Zip archive and add the files from the file list.
     *
     * @return bool
     */
    public function buildArchive()
    {
        if (!chdir($this->_workingDirectory)) {
            $this->_errorMessage .= "Can't change current directory to " . $this->_workingDirectory . "\n";
            return false;
        }

        if (!is_dir(dirname($this->_archiveFilename))) {
            mkdir(dirname($this->_archiveFilename), 0775, true);
        }
        $zipOpened = $this->_zip->open($this->_archiveFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($zipOpened !== true) {
            $this->_errorMessage .= Msd_Archive_Zip_Messages::getErrorMessage($zipOpened) . "\n";
            return false;
        }

        $this->_infoMessage .= "Archive created: " . $this->_archiveFilename . "\n";

        $filesAddedSuccess = true;
        foreach ($this->_fileList as $filename) {
            $fileAdded = $this->_zip->addFile($filename);
            $filesAddedSuccess = $filesAddedSuccess && $fileAdded;
            if ($fileAdded == true) {
                $this->_infoMessage .= "File added: $filename\n";
            } else {
                $this->_errorMessage .= "Can't add file $filename to the archive.\n";
            }
        }

        if (!$this->_zip->close()) {
            $this->_errorMessage .= "Can't close the archive.\n";
            return false;
        }

        $this->_infoMessage = "Archive closed.\n";

        return $filesAddedSuccess;
    }


    /**
     * Retrives the resulting filename of the archive.
     *
     * @return string
     */
    public function getArchiveFilename()
    {
        return $this->_archiveFilename;
    }

    /**
     * Returns the MIME type of the archive.
     *
     * @return string
     */
    public function getMimeType()
    {
        return 'application/zip';
    }
}

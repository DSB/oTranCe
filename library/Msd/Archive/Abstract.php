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
 * Abstract class for archive creator classes.
 *
 * @package         MySQLDumper
 * @subpackage      Archive
 */
abstract class Msd_Archive_Abstract
{
    /**
     * Basic path- and filename of the resulting archive, without file extension.
     *
     * @var string
     */
    protected $_baseArchiveName;

    /**
     * Path name, where the 'tar' command will be executed.
     * Normally this is the common root directory of the files beeing archived.
     * Default: current directory
     *
     * @var string
     */
    protected $_workingDirectory;

    /**
     * List of files that will be archived.
     *
     * @var array
     */
    protected $_fileList = array();

    /**
     * Information messages, such as success informations.
     *
     * @var string
     */
    protected $_infoMessage = '';

    /**
     * Error messages.
     *
     * @var string
     */
    protected $_errorMessage = '';

    /**
     * Class constructor
     *
     * @param string $baseArchiveName Path- and filename of the resulting archive, without extension.
     * @param string $workingDir      Working directiry for the archive adapter.
     */
    public function __construct($baseArchiveName, $workingDir = null)
    {
        $this->_baseArchiveName = $baseArchiveName;
        if ($workingDir === null) {
            $this->_workingDirectory = getcwd();
        } else {
            $this->_workingDirectory = $workingDir;
        }
        $this->init();
    }

    /**
     * Method for the archive adapter for specific initialization.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Adds a file to the list.
     *
     * @param string $filename Name of file to add.
     *
     * @return void
     */
    public function addFile($filename)
    {
        $this->_fileList[] = $filename;
    }

    /**
     * Adds the files, that are named in the array, to the complete file list.
     *
     * @param array $fileList List with filenames to add to archive.
     *
     * @return void
     */
    public function addFiles($fileList)
    {
        foreach ($fileList as $filename) {
            $this->addFile($filename);
        }
    }

    /**
     * Retireves the current file list.
     *
     * @return array
     */
    public function getFileList()
    {
        return $this->_fileList;
    }

    /**
     * Retrieves the information message of the archive adapter.
     *
     * @return string
     */
    public function getInfoMessage()
    {
        return $this->_infoMessage;
    }

    /**
     * Retrieves the error message of the archive adapter.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * Retrives the resulting filename of the archive.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getArchiveFilename();

    /**
     * Creates the archive
     *
     * @abstract
     *
     * @return bool
     */
    abstract public function buildArchive();

    /**
     * Returns the MIME type of the archive.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getMimeType();
}

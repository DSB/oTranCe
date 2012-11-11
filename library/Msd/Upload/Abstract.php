<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Users
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Class for controlling uploads.
 *
 * @package         MySQLDumper
 * @subpackage      Users
 */
abstract class Msd_Upload_Abstract
{
    /**
     * Path to the target direcory.
     *
     * @var string
     */
    protected $_targetDirectory;

    /**
     * Path- and filename of the temporary file.
     *
     * @var string
     */
    protected $_tempFilename;

    /**
     * Name of the form input.
     *
     * @var string
     */
    protected $_inputName;

    /**
     * Class constructor.
     *
     * @param string $targetDir Path to the target direcory.
     * @param string $inputName Name of the form input.
     *
     * @return \Msd_Upload_Abstract
     */
    public function __construct($targetDir, $inputName = 'qqfile')
    {
        $this->_tempFilename = tempnam('/tmp', 'PHP_');
        $this->_targetDirectory = $targetDir;
        $this->_inputName = $inputName;
    }

    /**
     * Checks the file extension of the uploaded file.
     *
     * @param array $allowedExtensions Array with allowed file extensions.
     *
     * @return bool
     */
    public function isFileTypeAllowed($allowedExtensions)
    {
        return in_array(pathinfo($this->getFilename(), PATHINFO_EXTENSION), $allowedExtensions);
    }

    /**
     * Returns the path- and filename of the temprary file.
     *
     * @return string
     */
    public function getTempFilename()
    {
        return $this->_tempFilename;
    }

    /**
     * Returns the path- and filename of the temprary file.
     *
     * @param string $tempFilename New path- and filename of the temporary file.
     *
     * @return null
     */
    public function setTempFilename($tempFilename)
    {
        $this->_tempFilename = $tempFilename;
    }

    /**
     * Returns the target directory.
     *
     * @return string
     */
    public function getTargetDirectory()
    {
        return $this->_targetDirectory;
    }

    /**
     * Sets the target directory.
     *
     * @param string $targetDirectory New target directoy.
     *
     * @return null
     */
    public function setTargetDirectory($targetDirectory)
    {
        $this->_targetDirectory = $targetDirectory;
    }

    /**
     * Returns the name of the input form element.
     *
     * @return string
     */
    public function getInputName()
    {
        return $this->_inputName;
    }

    /**
     * Sets the name of the input form element.
     *
     * @param string $inputName New name of the input form element.
     *
     * @return null
     */
    public function setInputName($inputName)
    {
        $this->_inputName = $inputName;
    }

    /**
     * Returns the real filename of uploaded file, including the target directory.
     *
     * @return string
     */
    public function getTargetFilename()
    {
        return rtrim($this->getTargetDirectory(), '/') . '/' . $this->getFilename();
    }

    /**
     * Returns the size of the uploaded file.
     *
     * @abstract
     *
     * @return int
     */
    abstract public function getFileSize();

    /**
     * Saves the uploaded file to the target directory.
     *
     * @abstract
     *
     * @return bool
     */
    abstract public function saveFile();

    /**
     * Returns the name of the uploaded file, without any paths.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getFilename();
}

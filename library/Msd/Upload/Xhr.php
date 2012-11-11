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
 * Class for controlling file uploads via XML-HTTP-Request.
 *
 * @package         MySQLDumper
 * @subpackage      Users
 */
class Msd_Upload_Xhr extends Msd_Upload_Abstract
{
    /**
     * Returns the size of the uploaded file.
     *
     * @return int
     */
    public function getFileSize()
    {
        if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int) $_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Msd_Upload_Exception('Getting content length is not supported.');
        }
    }

    /**
     * Saves the uploaded file to the target directory.
     *
     * @return bool
     */
    public function saveFile()
    {
        $phpInput = fopen("php://input", "r");
        $tempFile = fopen($this->getTempFilename(), "w+");
        $realSize = stream_copy_to_stream($phpInput, $tempFile);
        fclose($phpInput);

        if ($realSize != $this->getFileSize()) {
            return false;
        }

        $targetFile = fopen($this->getTargetFilename(), "w+");
        fseek($tempFile, 0, SEEK_SET);
        stream_copy_to_stream($tempFile, $targetFile);
        fclose($targetFile);
        fclose($tempFile);
        unlink($this->getTempFilename());

        return true;
    }

    /**
     * Returns the name of the uploaded file, without any paths.
     *
     * @return string
     */
    public function getFilename()
    {
        return $_REQUEST[$this->getInputName()];
    }

}

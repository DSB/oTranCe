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
 * Class for controlling file uploads via form.
 *
 * @package         MySQLDumper
 * @subpackage      Users
 */
class Msd_Upload_Form extends Msd_Upload_Abstract
{
    /**
     * Returns the size of the uploaded file.
     *
     * @return int
     */
    public function getFileSize()
    {
        return $_FILES[$this->getInputName()]['size'];
    }

    /**
     * Saves the uploaded file to the target directory.
     *
     * @return bool
     */
    public function saveFile()
    {
        $this->setTempFilename($_FILES[$this->getInputName()]['tmp_name']);

        return move_uploaded_file(
            $this->getTempFilename(),
            $this->getTargetFilename()
        );
    }

    /**
     * Returns the name of the uploaded file, without any paths.
     *
     * @return string
     */
    public function getFilename()
    {
        return $_FILES[$this->getInputName()]['name'];
    }

}

<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Archive_Tar
 * @version         SVN: $Rev: 2186 $
 * @author          $Author: dsb $
 */
/**
 * Class for .tar.gz. archive creation.
 *
 * @package         MySQLDumper
 * @subpackage      Archive_Tar
 */
class Msd_Archive_Tar_Gz extends Msd_Archive_Abstract
{
    /**
     * Complete path- and filename of the archive.
     *
     * @var string
     */
    protected $_archiveFilename;

    /**
     * Sets the final archive name.
     *
     * @return void
     */
    public function init()
    {
        $this->_archiveFilename = $this->_baseArchiveName . '.tar.gz';
    }

    /**
     * Retrieves the resulting filename of the archive.
     *
     * @return string
     */
    public function getArchiveFilename()
    {
        return $this->_archiveFilename;
    }

    /**
     * Creates the archive
     *
     * @return bool
     */
    public function buildArchive()
    {
        $process = new Msd_Process();
        $process->setCommand(sprintf('tar cvzf %s %s', $this->_archiveFilename, implode(' ', $this->_fileList)));
        $process->setWorkDir($this->_workingDirectory);
        $process->execute();
        $standardError = '';
        $standardOutput = '';
        while ($process->isRunning()) {
            $standardError .= $process->readError();
            $standardOutput .= $process->readOutput();
        }
        $this->_errorMessage = $standardError;
        $this->_infoMessage = $standardOutput;
        $process->close();

        return (bool) (strlen($standardError) == 0);
    }

    /**
     * Returns the MIME type of the archive.
     *
     * @return string
     */
    public function getMimeType()
    {
        return 'application/x-compressed-tar';
    }
}

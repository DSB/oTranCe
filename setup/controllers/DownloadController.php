<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://www.otrance.org
 *
 * @package         oTranCe
 * @subpackage      Setup_Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Controller for downloading the latest oTranCe release.
 *
 * @package         oTranCe
 * @subpackage      Setup_Controllers
 */
class DownloadController extends Setup_Controller_Abstract
{
    /**
     * Controller action to retrieve the information about the download package.
     *
     * @return void
     */
    public function packageAction()
    {
        if (!file_exists($this->_config['extractDir'])) {
            mkdir($this->_config['extractDir'], 0775, true);
        }
        $extractDir = realpath($this->_config['extractDir']);
        $setupInfo  = $_SESSION['setupInfo'];
        $this->_response->setBodyJson(
            array(
                'download'     => $setupInfo['package'],
                'downloadSize' => $setupInfo['filesize'],
                'extract'      => $extractDir,
            )
        );
        $tempFilename             = tempnam(sys_get_temp_dir(), 'otc');
        $_SESSION['tempFilename'] = $tempFilename;
    }

    /**
     * Fetches the number of downloaded bytes of the download package package.
     *
     * @return void
     */
    public function fetchDownloadedAction()
    {
        clearstatcache();
        $setupInfo = $_SESSION['setupInfo'];
        $result    = array();

        if (isset($_SESSION['tempFilename'])) {
            $tempFilename = $_SESSION['tempFilename'];
            session_write_close();
            if (file_exists($tempFilename)) {
                $filesize          = round(filesize($tempFilename), 0);
                $result['bytes']   = $filesize;
                $result['percent'] = ($filesize * 100) / $setupInfo['filesize'];
            }
        }

        $this->_response->setBodyJson($result);
    }

    /**
     * Controller action for downloading and extracting the OTC package.
     *
     * @return void
     */
    public function downloadAction()
    {
        $log = array('download' => false);
        $setupInfo = $_SESSION['setupInfo'];

        $tempFilename = $_SESSION['tempFilename'];
        $tempFile     = fopen($tempFilename, 'w+');

        session_write_close();
        $curlHandle = curl_init($setupInfo['package']);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandle, CURLOPT_FILE, $tempFile);
        $log['download'] = curl_exec($curlHandle);
        if (!$log['download']) {
            $log['downloadMessage'] = "Can't download oTranCe package.";
        }
        fclose($tempFile);

        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            $log['download']        = false;
            $log['downloadMessage'] = "Can't download oTranCe package.<br/>Server response HTTP code: $httpCode";
        }

        $log['filename'] = $tempFilename;
        $this->_response->setBodyJson($log);
    }

    /**
     * Controller action for extracting the downloaded OTC package.
     *
     * @return void
     */
    public function extractAction()
    {
        $log = array('extract' => false, 'dirsCreated' => true);
        $tempFilename = $_SESSION['tempFilename'];
        $zip = new ZipArchive();
        if ($zip->open($tempFilename) === true) {
            if (!file_exists($this->_config['extractDir'])) {
                mkdir($this->_config['extractDir'], 0775, true);
            }
            $extractDir     = realpath($this->_config['extractDir']);
            $log['extract'] = $zip->extractTo($extractDir);
            $zip->close();
            
            // Create directories for export and downloads
            if ($log['extract']) {
                $dirsToBeCreated = array();
                $dirsToBeCreated[] = '/data/downloads/';
                $dirsToBeCreated[] = '/data/export/';
                foreach ($dirsToBeCreated as $value) {
                    $value = str_replace('/', DIRECTORY_SEPARATOR, $value);
                    $log['dirsCreated'] &= $this->_createDir($extractDir . $value);
                }
            } else {
                $log['dirsCreated'] = false;
            }
        }
        unlink($tempFilename);

        $this->_response->setBodyJson($log);
    }
    
    /**
     * Creates a directory if it does not exist.
     *
     * @param string $path The path of the directory to be created.
     *
     * @return bool
     */
    protected function _createDir($path)
    {
        return is_dir($path) || @mkdir($path, 0775);
    }
}

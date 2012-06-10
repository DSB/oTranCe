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
        $setupInfo = $_SESSION['setupInfo'];
        $this->_response->setBodyJson(
            array(
                'download' => $setupInfo['package'],
                'extract' => $extractDir,
            )
        );
    }
    /**
     * Controller action for downloading and extracting the OTC package.
     *
     * @return void
     */
    public function downloadAction()
    {
        $log = array();
        $setupInfo = $_SESSION['setupInfo'];

        $tempFilename = tempnam(sys_get_temp_dir(), 'otc');
        $tempFile = fopen($tempFilename, 'w+');

        $curlHandle = curl_init($setupInfo['package']);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FILE, $tempFile);
        $log['download'] = curl_exec($curlHandle);
        fclose($tempFile);

        $zip = new ZipArchive();
        if ($zip->open($tempFilename) === true) {
            if (!file_exists($this->_config['extractDir'])) {
                mkdir($this->_config['extractDir'], 0775, true);
            }
            $extractDir = realpath($this->_config['extractDir']);
            $log['extract'] = $zip->extractTo($extractDir);
            $zip->close();
        }

        unlink($tempFilename);

        $this->_response->setBodyJson($log);
    }
}

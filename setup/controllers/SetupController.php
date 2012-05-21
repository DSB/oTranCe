<?php
class SetupController extends Setup_Controller_Abstract
{
    /**
     * Controller action for downloading the OTC package.
     *
     * @return void
     */
    public function downloadPackageAction()
    {
        $log = array();
        $log[] = "Fetching setup information from: {$this->_config['url']}";
        $curlHandle = curl_init($this->_config['url']);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $setupInfo = json_decode(curl_exec($curlHandle), true);

        $tempFilename = tempnam('/tmp', 'otc');
        $tempFile = fopen($tempFilename, 'w+');

        $log[] = "Downloading: {$setupInfo['package']}";
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curlHandle, CURLOPT_URL, $setupInfo['package']);
        curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FILE, $tempFile);
        curl_exec($curlHandle);
        fclose($tempFile);

        $zip = new ZipArchive();
        if ($zip->open($tempFilename) === true) {
            $extractDir = realpath(dirname(__FILE__) . '/../../temp/extracted/');
            $extractMessage = "Extracting to: $extractDir ";
            $extractMessage .= $zip->extractTo($extractDir) ? 'OK' : 'failed';
            $log[] = $extractMessage;
            $zip->close();
        }

        unlink($tempFilename);

        $this->_response->setBodyJson($log);
    }
}

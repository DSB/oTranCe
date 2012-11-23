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
 * Controller for requirement checks.
 *
 * @package         oTranCe
 * @subpackage      Setup_Controllers
 */
class RequirementsController extends Setup_Controller_Abstract
{
    /**
     * Status code for a passed required test.
     *
     * @const int
     */
    const REQUIREMENT_OK = 0;

    /**
     * Status code for a failed non-required test.
     *
     * @const int
     */
    const REQUIREMENT_WARNING = 1;

    /**
     * Status code for a failed required test.
     *
     * @const int
     */
    const REQUIREMENT_ERROR = 2;

    /**
     * Results of the requiments checks.
     *
     * @var array
     */
    protected $_requirements = array();

    /**
     * Array with loaded extensions.
     *
     * @var array
     */
    protected $_loadedExtensions;

    /**
     * Array with disabled functions.
     *
     * @var array
     */
    protected $_disabledFunctions;

    /**
     * Adds a check result to the results list.
     *
     * @param string $requirement Name of the requirement.
     * @param mixed  $status      Status
     * @param string $value       Systems value.
     *
     * @return void
     */
    protected function _addCheckResult($requirement, $status, $value = 'installed')
    {
        if (is_bool($status)) {
            $status = $status ? self::REQUIREMENT_OK : self::REQUIREMENT_ERROR;
        }

        $this->_requirements[$requirement] = array(
            'status' => $status,
            'value'  => $value,
            'passed' => ($status != self::REQUIREMENT_ERROR),
        );
    }

    /**
     * Checks for the existence of a PHP extension.
     *
     * @param string $extensionName Name of the extension.
     * @param bool   $required      Determines if the extension is required.
     *
     * @return void
     */
    protected function _checkExtension($extensionName, $required = true)
    {
        $checkResult = in_array($extensionName, $this->_loadedExtensions);
        $status      = self::REQUIREMENT_OK;
        if (!$checkResult) {
            $status = $required ? self::REQUIREMENT_ERROR : self::REQUIREMENT_WARNING;
        }

        $this->_addCheckResult($extensionName, $status);
    }

    /**
     * Checks for the existence of a PHP class.
     *
     * @param string $className Name of the class.
     * @param bool   $required  Determines if the class is required.
     *
     * @return void
     */
    protected function _checkClass($className, $required = true)
    {
        try {
            $checkResult = class_exists($className);
            $status      = self::REQUIREMENT_OK;
        } catch (Exception $e) {
            $checkResult = false;
        }

        if (!$checkResult) {
            $status = $required ? self::REQUIREMENT_ERROR : self::REQUIREMENT_WARNING;
        }

        $this->_addCheckResult($className, $status);
    }

    /**
     * Checks for the existence of a PHP function.
     *
     * @param string $functionName Name of the function
     * @param bool   $required     Determines if the function is required.
     *
     * @return void
     */
    protected function _checkFunction($functionName, $required = true)
    {
        $checkResult = function_exists($functionName) && !in_array($functionName, $this->_disabledFunctions);
        $status      = self::REQUIREMENT_OK;
        if (!$checkResult) {
            $status = $required ? self::REQUIREMENT_ERROR : self::REQUIREMENT_WARNING;
        }

        $this->_addCheckResult($functionName, $status);
    }

    /**
     * Checks the given filename for write capability.
     *
     * @param string $id       ID for the check.
     * @param string $filename Filename to check.
     * @param bool   $required This check is required to continue.
     *
     * @return void
     */
    protected function _checkWritable($id, $filename, $required = true)
    {
        clearstatcache();
        if (!file_exists($filename)) {
            mkdir($filename, 0777, true);
        }

        clearstatcache();
        $checkResult = file_exists($filename);
        if (!is_writable($filename)) {
            chmod($filename, 0777);
        }

        clearstatcache();
        $checkResult = $checkResult && is_writable($filename);
        $status      = self::REQUIREMENT_OK;
        $value       = 'writable';
        if (!$checkResult) {
            $status = $required ? self::REQUIREMENT_ERROR : self::REQUIREMENT_WARNING;
            $value  = 'not writable';
        }

        $this->_addCheckResult($id, $status, $value);
    }

    /**
     * Action for retrieving update information.
     *
     * @return void
     */
    public function fetchInfoAction()
    {
        $curlHandle = curl_init($this->_config['url'] . '?module=otrance_' . $this->_config['version']);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $rawResponse = curl_exec($curlHandle);
        $setupInfo   = json_decode($rawResponse, true);

        if ($setupInfo === null) {
            $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
            $message  = "The update server sends an invalid response.";
            if ($httpCode != 200) {
                $message = "Server response HTTP code: $httpCode";
            }
            $this->_response->setBodyJson(
                array(
                    'error' => "Can't fetch package information.<br/>$message",
                )
            );

            return;
        }

        if (isset($setupInfo['error'])) {
            $this->_response->setBodyJson(
                array(
                    'error' => "Can't fetch package information.<br/>Server message: " . $setupInfo['error'],
                )
            );

            return;
        }

        $_SESSION['setupInfo'] = $setupInfo;
        $jsonArray             = array();
        foreach ($setupInfo['requirements'] as $requireKey => $requirement) {
            $requirement['reqKey'] = $requireKey;
            $jsonArray[]           = $requirement;
        }
        $this->_response->setBodyJson(
            array(
                'version'      => $setupInfo['version'],
                'requirements' => $jsonArray,
            )
        );
    }

    /**
     * Action for checking the requirements.
     *
     * @return void
     */
    public function checkAction()
    {
        $this->_disabledFunctions = explode(',', ini_get('disable_functions'));
        $this->_loadedExtensions  = get_loaded_extensions();

        $setupInfo = $_SESSION['setupInfo']['requirements'];

        $this->_addCheckResult(
            'php_version',
            (version_compare(PHP_VERSION, $setupInfo['php_version']['value']) >= 0),
            PHP_VERSION
        );

        unset($setupInfo['php_version']);

        if (isset($setupInfo['sapi'])) {
            $result = self::REQUIREMENT_OK;
            if ((PHP_SAPI == $setupInfo['sapi']['value'])) {
                $result = self::REQUIREMENT_WARNING;
                if ($setupInfo['sapi']['required']) {
                    $result = self::REQUIREMENT_ERROR;
                }
            }
            $this->_addCheckResult('sapi', $result, PHP_SAPI);
            unset($setupInfo['sapi']);
        }

        foreach ($setupInfo as $requireKey => $requirement) {
            if ($requirement['type'] == 'extension') {
                $this->_checkExtension($requireKey, $requirement['required']);
            }

            if ($requirement['type'] == 'function') {
                $this->_checkFunction($requireKey, $requirement['required']);
            }

            if ($requirement['type'] == 'class') {
                $this->_checkClass($requireKey, $requirement['required']);
            }

            if ($requirement['type'] == 'writable') {
                $this->_checkWritable($requireKey, $requirement['value'], $requirement['required']);
            }
        }

        $this->_response->setBodyJson($this->_requirements);
    }
}

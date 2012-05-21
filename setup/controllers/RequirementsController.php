<?php
class RequirementsController extends Setup_Controller_Abstract
{
    const REQUIREMENT_OK = 0;
    const REQUIREMENT_WARNING = 1;
    const REQUIREMENT_ERROR = 2;

    protected $_requirements = array();

    protected $_loadedExtensions;

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

        $this->_requirements[$requirement] = array('status' => $status, 'value' => $value);
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
        $status = self::REQUIREMENT_OK;
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
        $checkResult = class_exists($className);
        $status = self::REQUIREMENT_OK;
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
        $status = self::REQUIREMENT_OK;
        if (!$checkResult) {
            $status = $required ? self::REQUIREMENT_ERROR : self::REQUIREMENT_WARNING;
        }

        $this->_addCheckResult($functionName, $status);
    }

    /**
     * Action for checking the requirements.
     *
     * @return void
     */
    public function checkAction()
    {
        $this->_disabledFunctions = explode(',', ini_get('disable_functions'));
        $this->_loadedExtensions = get_loaded_extensions();

        $this->_addCheckResult('php_version', (version_compare(PHP_VERSION, '5.2.10') >= 0), PHP_VERSION);
        $this->_addCheckResult(
            'SAPI',
            (PHP_SAPI == 'isapi') ? self::REQUIREMENT_WARNING : self::REQUIREMENT_OK,
            PHP_SAPI
        );
        $this->_checkExtension('curl');
        $this->_checkExtension('zip');
        $this->_checkExtension('mysqli');
        $this->_checkExtension('mcrypt');
        $this->_checkExtension('tokenizer');
        $this->_checkExtension('xmlreader', false);
        $this->_checkExtension('zlib');
        $this->_checkFunction('proc_open');
        $this->_checkClass('ZipArchive');

        $this->_response->setBodyJson($this->_requirements);
    }
}

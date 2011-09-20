<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Bootstrap class
 *
 * @package         MySQLDumper
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initialize action helpers.
     *
     * @return void
     */
    public function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new Msd_Action_Helper_AssignConfigAndLanguage()
        );
    }

    /**
     * Start session
     *
     * Anything else is set in configs/application.ini
     *
     * @return void
     */
    public function _initApplication()
    {
        Zend_Session::setOptions(array('strict' => true));
        Zend_Session::start();

        $moduleLoader = new Msd_Module_Loader(
            array(
                 'Module_' => realpath(APPLICATION_PATH . '/../modules/library/')
            )
        );

        Zend_Loader_Autoloader::getInstance()->pushAutoloader($moduleLoader, 'Module_');

        // check if server has magic quotes enabled and normalize params
        if ( (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() == 1)) {
            $_POST = Bootstrap::stripslashes_deep($_POST);
        }
    }

    /**
     * Initialize configuration.
     *
     * @return void
     */
    public function _initConfiguration()
    {
        $dynamicConfig = new Msd_Config_Dynamic();
        $configFile = $dynamicConfig->getParam('configFile', 'defaultConfig.ini');
        $config = new Msd_Config(
            'Default',
            array('directories' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs')
        );
        $config->load($configFile);
        Msd_Registry::setConfig($config);

        Msd_Registry::setDynamicConfig($dynamicConfig);
    }

    /**
     *
     * Set Firebug_logger in registry
     *
     * @return void
     */
    public function _initFirebugLogger()
    {
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        Zend_Registry::set('logger', $logger);
    }

    /**
     * Un-quote a string or array
     *
     * @param string|array $value The value to strip
     *
     * @return string|array
     */
    public static function stripslashes_deep($value)
    {
        $value = is_array($value) ? array_map('Bootstrap::stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }

}

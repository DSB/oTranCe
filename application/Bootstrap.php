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
    //print_r(get_include_path());die();
        Zend_Session::setOptions(array('strict' => true));
        Zend_Session::start();
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
            array(
                'directories' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs',
            )
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
}

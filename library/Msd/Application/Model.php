<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Darky
 * Date: 20.08.11
 * Time: 16:00
 * To change this template use File | Settings | File Templates.
 */
 
abstract class Msd_Application_Model
{
    /**
     * @var Msd_Config
     */
    protected $_config;

    /**
     * @var Msd_Config_Dynamic
     */
    protected $_dynamicConfig;

    /**
     * @var Msd_Db_Mysqli
     */
    protected $_dbo;

    /**
     * @var string
     */
    protected $_database;

    public function __construct()
    {
        $this->_config = Msd_Registry::getConfig();
        $this->_dynamicConfig = Msd_Registry::getDynamicConfig();
        $this->_dbo = Msd_Db::getAdapter();
        $dbUserConfig = $this->_config->getParam('dbuser');
        $this->_database = $dbUserConfig['db'];
        $this->_dbo->selectDb($this->_database);

        $this->init();
    }

    public function init()
    {
    }
}

<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Application_Model
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * Abstract Application Model class
 *
 * @package         MySQLDumper
 * @subpackage      Application_Model
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

    /**
     * @var string
     */
    protected $_tablePrefix;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->_config        = Msd_Registry::getConfig();
        $this->_dynamicConfig = Msd_Registry::getDynamicConfig();
        $this->_dbo           = Msd_Db::getAdapter();
        $this->_database      = $this->_config->getParam('dbuser.db');
        $this->_tablePrefix   = $this->_config->getParam('dbuser.tablePrefix');
        $this->_dbo->selectDb($this->_database);
        $this->init();
    }

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Get nr of rows of last query (query needs to invoked using SQL_CALC_FOUND_ROWS)
     *
     * @return integer
     */
    public function getRowCount()
    {
        return (int)$this->_dbo->getRowCount();
    }
}

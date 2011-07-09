<?php

class Application_Model_FileTemplates
{

    /**
     * Configuration object
     * @var \Msd_Configuration
     */
    private $_config;

    /**
     * Database object
     * @var Msd_Db_Mysqli
     */
    private $_dbo;

    /**
     * Database name containing the tables
     * @var array|string
     */
    private $_database;

    /**
     * Database table containing language var keys
     * @var string
     */
    private $_tableFiletemplates;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_config = Msd_Configuration::getInstance();
        $this->_database = $this->_config->get('config.dbuser.db');
        $this->_tableFiletemplates = $this->_config->get('config.table.filetemplates');
        $this->_dbo = Msd_Db::getAdapter();
        $this->_dbo->selectDb($this->_database);
    }

    /**
     * Get file templates
     *
     * @return array
     */
    public function getFileTemplates()
    {
        $sql = 'SELECT * FROM `' . $this->_database . '`.`' . $this->_tableFiletemplates .'`';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return $res;
    }
}

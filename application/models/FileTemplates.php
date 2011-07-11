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
     * @param string $order       Name of the column to order the file list
     * @param string $filter      String to filter the templates (effects lang locale and lang name)
     * @param int    $offset      Offset of entry where the result starts
     * @param int    $recsPerPage Number of records per page
     *
     * @return array
     */
    public function getFileTemplates($order, $filter = '', $offset = 0, $recsPerPage = 0)
    {
        $where = '';
        $limit = '';
        $order = $this->_dbo->escape($order);
        if ($filter > '') {
            $filter = $this->_dbo->escape($filter);
            $where = "WHERE `name` LIKE '%$filter%' OR `filename` LIKE '%$filter%'";
        }
        if ($recsPerPage > 0) {
            $recsPerPage = $this->_dbo->escape($recsPerPage);
            $offset = $this->_dbo->escape($offset);
            $limit = "LIMIT $offset, $recsPerPage";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$this->_database}`.`{$this->_tableFiletemplates}` $where
            ORDER BY `$order` $limit";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return $res;
    }

    /**
     * Get nr of rows of last query (needs to invoked using SQL_CALC_FOUND_ROWS)
     *
     * @return integer
     */
    public function getRowCount()
    {
        return $this->_dbo->getRowCount();
    }
}

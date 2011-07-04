<?php
/**
 * Created by JetBrains PhpStorm.
 * User: David
 * Date: 30.04.11
 * Time: 15:13
 * To change this template use File | Settings | File Templates.
 */

class Application_Model_Admin {
    /**
     * Database object
     * @var \MsdDbFactory
     */
    private $_dbo;

    /**
     * Configuration object
     * @var \Msd_Configuration
     */
    private $_config;

    public function __construct()
    {
        $this->_config = Msd_Configuration::getInstance();
        $this->_database = $this->_config->get('config.dbuser.db');
        $this->_dbo = Msd_Db::getAdapter();
    }

    /**
     * Get list of users and their language rights
     *
     * @param int    $offset         Offset for records to fecth
     * @param int    $recordsPerPage Number of records per page
     * @param string $filter String to find in records
     *
     * @return array
     */
    public function getUsers($offset = 0, $recordsPerPage = 10, $filter = '')
    {
        $this->_dbo->selectDb($this->_database);
        $sql = 'SELECT SQL_CALC_FOUND_ROWS u.`id`, u.`name`, '
               . 'GROUP_CONCAT(rl.`languageId` SEPARATOR ",") as `languages` '
               . 'FROM `' . $this->_table . '` u '
               . 'LEFT JOIN `lang_userrights_language` rl ON rl.`userId` = u.`id`';
        if ($filter > '') {
            $sql .= ' WHERE `name` LIKE \'%'. $this->_dbo->escape($filter) . '%\'';
        }
        $sql .= ' GROUP BY u.`id`';
        $sql .= ' LIMIT '. intval($offset). ', ' . intval($recordsPerPage);
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
        $res = $this->_dbo->query('SELECT FOUND_ROWS() AS `results`', Msd_Db::ARRAY_ASSOC);
        if (!isset($res[0]['results'])) {
            return 0;
        }
        return (int) $res[0]['results'];
    }

}

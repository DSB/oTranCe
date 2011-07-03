<?php

class Application_Model_Statistics {

    /**
     * Database object
     * @var \MsdDbFactory
     */
    private $_dbo;

    /**
     * Tablename of history table
     * @var string
     */
    private $_tableHistory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_dbo = Msd_Db::getAdapter();
        $config = Msd_Configuration::getInstance();
        $this->_dbo->selectDb($config->get('config.dbuser.db'));
        $this->_tableHistory = $config->get('config.table.history');
    }

    /**
     * Get array with user statistics
     *
     * @return array
     */
    public function getUserstatistics()
    {
        $sql = 'SELECT `user_id`, `lang_id`, count(*) as `editActions` FROM `' . $this->_tableHistory .'`';
        $sql .= ' WHERE `action`=\'changed\' GROUP BY `user_id`, `lang_id`';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return $res;
    }
}

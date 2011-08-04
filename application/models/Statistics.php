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
        $this->_tableLanguages = $config->get('config.table.languages');
    }

    /**
     * Get array with user statistics
     *
     * @return array
     */
    public function getUserstatistics()
    {
        $sql = "SELECT h.`user_id`, h.`lang_id`, count(*) as `editActions`
            FROM `{$this->_tableHistory}` h
            LEFT JOIN `{$this->_tableLanguages}` l ON l.`id` = h.`lang_id`
            WHERE h.`action`='changed' AND l.`active` = 1
            GROUP BY h.`user_id`, h.`lang_id`";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return $res;
    }
}

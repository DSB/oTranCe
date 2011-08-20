<?php
/**
 * mmodel to manage statistics.
 */
class Application_Model_Statistics extends Msd_Application_Model
{
    /**
     * Tablename of history table
     * @var string
     */
    private $_tableHistory;

    /**
     * Name of languages table
     * @var string
     */
    private $_tableLanguages;

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $tableConfig = $this->_config->getParam('table');
        $this->_tableHistory = $tableConfig['history'];
        $this->_tableLanguages = $tableConfig['languages'];
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

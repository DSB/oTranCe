<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Models
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * Statistics model
 *
 * @package         oTranCe
 * @subpackage      Models
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
     * Name of users table
     * @var string
     */
    private $_tableUsers;

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $tableConfig           = $this->_config->getParam('table');
        $this->_tableHistory   = $tableConfig['history'];
        $this->_tableLanguages = $tableConfig['languages'];
        $this->_tableUsers     = $tableConfig['users'];
    }

    /**
     * Get array with user change action count per language
     *
     * @return array
     */
    public function getUserstatistics()
    {
        $sql = "SELECT h.`user_id`, h.`lang_id`, u.`username`, l.locale, count(*) as `editActions`
            FROM `{$this->_tableHistory}` h
            LEFT JOIN `{$this->_tableLanguages}` l ON l.`id` = h.`lang_id`
            LEFT JOIN `{$this->_tableUsers}` u ON u.`id` = h.`user_id`
            WHERE h.`action`='changed' AND l.`active` = 1 AND u.`active` = 1
            GROUP BY h.`user_id`, h.`lang_id` ORDER BY u.`username` ASC, l.`locale` ASC";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        return $res;
    }

    /**
     * Get array with user overall change action count
     *
     * @return array
     */
    public function getUserChangeStatistics()
    {
        $sql = "SELECT `user_id`, count(*) as `editActions`, max(`dt`) as `lastAction`
            FROM `{$this->_tableHistory}` h
            LEFT JOIN `{$this->_tableUsers}` u ON u.`id` = h.`user_id`
            WHERE u.`active`= 1 AND h.`action`='changed'
            GROUP BY h.`user_id` ORDER BY h.`dt` DESC";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        $ret = array();
        foreach ($res as $data) {
            $ret[$data['user_id']] = $data;
        }
        return $ret;
    }

}

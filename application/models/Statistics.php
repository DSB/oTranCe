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
        $tableConfig               = $this->_config->getParam('table');
        $this->_tableHistory       = $tableConfig['history'];
        $this->_tableHistoryChange = $tableConfig['history_change'];
        $this->_tableLanguages     = $tableConfig['languages'];
        $this->_tableUsers         = $tableConfig['users'];
    }

    /**
     * Get array with user change action count per language
     *
     * @return array
     */
    public function getUserstatistics()
    {
        $sql = "SELECT h.`user_id`, h.`lang_id`, u.`username`, l.locale, count(*) AS `editActions`
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
        $sql = "SELECT `user_id`, count(*) AS `editActions`, max(`dt`) AS `lastAction`
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

    /**
     * Get array with all users and their overall change action count
     *
     * @param string $filter         Filter for user name
     * @param int    $offset         Offset in db
     * @param int    $recordsPerPage Nr of records to fetch
     * @param string $sortField      Field to sort the list
     * @param int    $sortDirection  Sort direction SORT_ASC / SORT_DESC
     *
     * @return array
     */
    public function getUserOverallStatistics($filter = '', $offset = 0, $recordsPerPage = 0, $sortField = 'username', $sortDirection = SORT_ASC)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS u.`id`, u.`username`, u.`realName`, u.`email`, u.`active`,
            h.`editActions`, h.`lastAction`
            FROM `{$this->_tableUsers}` u
            LEFT JOIN `{$this->_tableHistoryChange}` h ON h.`user_id` = u.`id`";

        if ($filter > '') {
            $sql .= ' WHERE u.`username` LIKE \'%' . $this->_dbo->escape($filter) . '%\' ';
        }

        $sql .= ' GROUP BY u.`id` ORDER BY `' . $sortField . '` ' . ($sortDirection == SORT_ASC ? 'ASC' : 'DESC');

        if ($sortField != 'username') {
            $sql .= ', `username` ASC';
        }

        if ($recordsPerPage != 0) {
            $sql .= ' LIMIT ' . $offset . ',' . $recordsPerPage;
        }

        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        return $res;
    }

}

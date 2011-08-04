<?php

class Application_Model_History {

    /**
     * Configuration object
     * @var \MsdConfiguration
     */
    private $_config;

    /**
     * Database object
     * @var \MsdDbFactory
     */
    private $_dbo;

    /**
     * Database name
     * @var string
     */
    private $_database;

    /**
     * Name of table containing history data
     * @var string
     */
    private $_tableHistory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_dbo = Msd_Db::getAdapter();
        $this->_config = Msd_Configuration::getInstance();
        $this->_database = $this->_config->get('config.dbuser.db');
        $this->_tableHistory = $this->_config->get('config.table.history');
        $this->_dbo->selectDb($this->_database);
    }

    /**
     * Get entries from history table
     *
     * @param int        $offset
     * @param int        $nr
     * @param int        $filterLanguage
     * @param string|int $filterUser
     * @param string|int $filterAction
     *
     * @return array
     */
    public function getEntries($offset = 0, $nr = 50, $filterLanguage = 0, $filterUser = 0, $filterAction = 0)
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS h.*, k.`id` as `key_id`, k.`key` FROM `'.$this->_tableHistory .'` h ';
        $sql .= ' LEFT JOIN `' . $this->_config->get('config.table.keys').'` k ON h.`key_id` = k.`id`';
        $sql .= ' WHERE 1';
        if ($filterLanguage > 0) {
            $sql .= ' AND `lang_id`=' . intval($filterLanguage);
        }
        if ($filterUser > 0) {
            $sql .= ' AND `user_id`=\'' . $filterUser .'\'';
        }
        if (!is_numeric($filterAction)) {
            if (strpos($filterAction, '%') !== false) {
                $sql .= ' AND `action` LIKE \'' . $filterAction .'\'';
            } else {
                $sql .= ' AND `action`=\'' . $filterAction .'\'';
            }
        }
        $sql .= ' ORDER BY `dt` DESC'
               .' LIMIT '.$offset.','.$nr;
        return $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
    }

    /**
     * Compare old with new values and log changes
     *
     * @param  string $keyId
     * @param  array $oldValues
     * @param  array $newValues
     *
     * @return void
     */
    public function logChanges($keyId, $oldValues, $newValues)
    {
        foreach ($oldValues as $lang => $val) {
            if ($newValues[$lang] !== $val) {
                $this->saveChange($keyId, $lang, $val, $newValues[$lang]);
            }
        }
    }

    /**
     * Log creation of a new language variable
     *
     * @param  string $name
     * @return void
     */
    public function logNewVarCreated($name)
    {
        $this->saveChange($name, 0, '-', '-', 'created');
    }

    /**
     * Log deletion of a language variable
     *
     * @param  string $key
     * @return void
     */
    public function logVarDeleted($key)
    {
        $this->saveChange(0, 0, '-', '-', 'deleted \'' . $key .'\'');
    }

    /**
     * Save change to history table in database
     *
     * @param string $keyId   The key to save, 0 if not referring to a key
     * @param string $lang_id Language
     * @param string $oldVal
     * @param string $newVal
     * @param string $action
     * @param bool   $time    If false, set current timte
     *
     * @return void
     */
    public function saveChange($keyId, $lang_id, $oldVal, $newVal, $action = 'changed', $time = false)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        if ($oldVal == '') {
            $oldVal = '-';
        }
        if ($time == false) {
            $time = date('Y-m-d H-i-s', time());
        }
        $sql = 'INSERT INTO `' . $this->_database . '`.`' . $this->_tableHistory
               . '` (`user_id`, `dt`, `key_id`, `action`, `lang_id`,`oldValue`,`newValue`)'
               .' VALUES ('
               . intval($auth['id']) . ', '
               .'\'' . $time .'\', '
               . intval($keyId) . ', '
               .'\'' . $this->_dbo->escape($action) .'\', '
               . intval($lang_id) . ', '
               .'\'' . $this->_dbo->escape($oldVal) . '\', '
               .'\'' . $this->_dbo->escape($newVal) . '\')';
        $this->_dbo->query($sql);
    }

    /**
     * Delete entry by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id) {
        $sql = 'DELETE FROM `' . $this->_tableHistory . '` WHERE `id` = '.intval($id) . ' LIMIT 1';
        return $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Get latest change of the langugae
     *
     * @param string $lang_id
     * @return string
     */
    public function getLatestChange($lang_id)
    {
        $lang_id = (int) $lang_id;
        $sql = 'SELECT `dt` FROM `'.$this->_tableHistory . '`'
                .' WHERE `lang_id`=' . $lang_id .' OR `lang_id`=0 ORDER BY `dt` DESC LIMIT 1';
        $res =$this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return isset($res[0]['dt']) ? $res[0]['dt'] : '';
    }

    /**
     * Get nr of rows of last query (query needs to invoked using SQL_CALC_FOUND_ROWS)
     *
     * @return integer
     */
    public function getRowCount()
    {
        return (int) $this->_dbo->getRowCount();
    }

    /**
     * Log action login failed
     *
     * @param $user Name of user that tried to log in
     *
     * @return void
     */
    public function logLoginFailed($user)
    {
        $this->saveChange('-', 0, '-', '-', '<i>'. $user . '</i> failed to log in');
    }

    /**
     * Log action login ok
     *
     * @return void
     */
    public function logLoginSuccess()
    {
        $this->saveChange('-', 0, '-', '-', 'logged in');
    }

    /**
     * Log action log out
     *
     * @return void
     */
    public function logLogout()
    {
        $this->saveChange('-', 0, '-', '-', 'logged out');
    }

    /**
     * Log action svn update
     *
     * @param int $langId Id of language
     *
     * @return void
     */
    public function logSvnUpdate($langId)
    {
        $this->saveChange('-', $langId, '-', '-', 'updated SVN');
    }

    /**
     * Log action svn update for all languages
     *
     * @return void
     */
    public function logSvnUpdateAll()
    {
        $this->saveChange('-', 0, '-', '-', 'updated SVN');
    }
}

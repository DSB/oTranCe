<?php

class Application_Model_User {

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
     * Configuration
     * @var \Msd_Configuration
     */
    private $_config;

    /**
     * Name of current user
     * @var
     */
    private $_username;

    /**
     * Id of current user
     * @var
     */
    private $_userId;

    /**
     * User table
     * @var array|string
     */
    private $_tableUsers;

    /**
     * Rights of the current user
     * @var array
     */
    private $_userrights;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_config = Msd_Configuration::getInstance();
        $this->_database = $this->_config->get('config.dbuser.db');
        $this->_tableUsersettings = $this->_config->get('config.table.usersettings');
        $this->_tableUserrights = $this->_config->get('config.table.userrights');
        $this->_tableUsers = $this->_config->get('config.table.users');;
        $this->_dbo = Msd_Db::getAdapter();
        $auth =Zend_Auth::getInstance()->getIdentity();
        $this->_username = $auth['name'];
        $this->_userId = $auth['id'];
    }

    /**
     * Get list of translators with edit rights for language
     *
     * Return ass. array[lang_id] = user_id
     *
     * @return array
     */
    public function getTranslators()
    {
        $ret = array();
        $this->_dbo->selectDb($this->_database);
        $sql = 'SELECT * FROM `' . $this->_tableUserrights .'`'
                .' WHERE `right`=\'edit\' ORDER BY `value` ASC';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        foreach ($res as $val) {
            $ret[$val['value']][] = $val['user_id'];
        }
        $this->_tableUsers = $ret;
        return $ret;
    }

    /**
     * Get list of users
     *
     * @param string $filter         Filter for user name
     * @param int    $offset         Offset ind db
     * @param int    $recordsPerPage Nr of records to fetch
     *
     * @return array
     */
    public function getUsers($filter = '', $offset = 0, $recordsPerPage = 0)
    {
        $ret = array();
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `'.$this->_database.'`.`'.$this->_tableUsers . '`';
        if ($filter > '') {
            $sql .= ' WHERE `username` LIKE \'%' . $this->_dbo->escape($filter) .'%\'';
        }

        $sql .= ' ORDER BY `username` ASC';
        if ($recordsPerPage != 0) {
            $sql .= ' LIMIT ' . $offset . ',' . $recordsPerPage;
        }

        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        foreach ($res as $val) {
            $ret[$val['id']] = $val['username'];
        }
        natcasesort($ret);
        return $ret;
    }

    /**
     * Get a user by his id
     *
     * @param int $userId Id of user
     *
     * @return array
     */
    public function getUserById($userId)
    {
        $userId = (int) $userId;
        $sql = 'SELECT * FROM `'.$this->_database.'`.`'.$this->_tableUsers . '`'
                .' WHERE `id`= ' . $userId;
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        return isset($res[0]) ? $res[0] : array();
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
     * Get user settings for a param and return as numeric array
     *
     * @param string $name       Name of setting to read
     * @param mixed  $default    Default value if no value is found in db
     * @param bool   $forceArray
     *
     * @return mixed
     */
    public function loadSetting($name, $default = '', $forceArray = false)
    {
        if ($forceArray === true) {
            $default = array();
        }
        $sql = 'SELECT `value` FROM `'.$this->_database.'`.`'.$this->_tableUsersettings . '` '
                . 'WHERE `user_id`=\''.$this->_userId.'\' '
                . 'AND `setting`=\''.$name.'\' ORDER BY `value` ASC';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        if (isset($res[0])) {
            if ($forceArray === false && count($res) == 1) {
                return $res[0]['value'];
            }
            $ret = array();
            foreach ($res as $val) {
                $ret[] = $val['value'];
            }
            return $ret;
        } else {
            return $default;
        }
    }

    /**
     * Save user settings to db
     *
     * @param  string       $name   The setting to save to db
     * @param  string|array $values The value to save to db
     *
     * @return boolean
     */
    public function saveSetting($name, $values)
    {
        $this->_dbo->selectDb($this->_database);
        // delete old entries
        $sql = 'DELETE FROM `'.$this->_tableUsersettings . '` WHERE '
                . '`user_id`=' . $this->_userId .' AND `setting`=\''. $name .'\'';
        $this->_dbo->query($sql, Msd_Db::SIMPLE);

        if (!is_array($values)) {
            $values = array($values);
        }
        if (!isset($values[0]) || $values[0] == '') {
            // nothing to save -> return
            return true;
        }
        $paramPattern = '(%s,\'%s\',%d)';
        $params = '';
        foreach ($values as $value) {
            $params .= sprintf($paramPattern, $this->_userId, $name, $value) .', ';
        }
        $params = substr($params, 0, -2);

        $sql = 'INSERT INTO `'.$this->_tableUsersettings . '`'
                    . ' (`user_id`,`setting`,`value`) VALUES '.$params;
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return $res;
    }

    /**
     * Get user rights
     *
     * @param string $right  If set, reduce return to considered right
     * @param int    $userId Id of user, if not set use the current user
     *
     * @return array
     */
    public function getUserRights($right = '', $userId = 0)
    {
        if ($userId == 0) {
            $userId = $this->_userId;
        }
        $sql = 'SELECT * FROM `'.$this->_database.'`.`' . $this->_tableUserrights . '`'
                .' WHERE `user_id`=\''.$userId.'\'';
        if ($right > '') {
            $sql .= ' AND `right` = \'' . $right .'\'';
        }
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        $ret = array();
        foreach ($res as $val) {
            $ret[] = $val['value'];
        }
        return $ret;
    }

    /**
     * Get user global rights (all except edit rights of languages)
     *
     * @return array
     */
    public function getUserGlobalRights()
    {
        $sql = 'SELECT * FROM `'.$this->_database.'`.`' . $this->_tableUserrights . '`'
                .' WHERE `user_id`=\''.$this->_userId.'\' AND NOT `right`=\'edit\'';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        $ret = array();
        foreach ($res as $r) {
            $ret[$r['right']] = $r['value'];
        }
        $this->_userrights = $ret;
        return $ret;
    }

    /**
     * Checks if the current user has a right set to value
     *
     * @param string $right The right to check
     * @param int    $value The value the rights must have
     *
     * @return bool
     */
    public function hasRight($right, $value = 1)
    {
        if ($this->_userrights === null) {
            $this->getUserGlobalRights();
        }
        if (isset($this->_userrights[$right])) {
            if ($this->_userrights[$right] == $value) {
                return true;
            }
        }
        return false;
    }
}

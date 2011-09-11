<?php
/**
 * Model to manage users.
 */
class Application_Model_User extends Msd_Application_Model
{
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
     * @var string
     */
    private $_tableUsers;

    /**
     * Usersettings table
     * @var string
     */
    private $_tableUsersettings;

    /**
     * Userrights table
     * @var string
     */
    private $_tableUserrights;

    /**
     * User language edit rights table
     * @var string
     */
    private $_tableUserLanguages;

    /**
     * Language table
     * @var string
     */
    private $_tableLanguages;

    /**
     * Rights of the current user
     * @var array
     */
    private $_userrights;

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $tableConfig = $this->_config->getParam('table');
        $this->_tableUsersettings = $tableConfig['usersettings'];
        $this->_tableUserrights = $tableConfig['userrights'];
        $this->_tableLanguages = $tableConfig['languages'];
        $this->_tableUserLanguages = $tableConfig['user_languages'];
        $this->_tableUsers = $tableConfig['users'];
        $auth = Zend_Auth::getInstance()->getIdentity();
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
        $sql = 'SELECT * FROM `' . $this->_tableUserLanguages .'`';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        foreach ($res as $val) {
            $ret[$val['language_id']][] = $val['user_id'];
        }
        $this->_tableUsers = $ret;
        return $ret;
    }

    /**
     * Get list of users
     *
     * @param string $filter         Filter for user name
     * @param int    $offset         Offset in db
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
            $ret[$val['id']] = $val;
        }
        return $ret;
    }

    /**
     * Get list of user names (key is the user id)
     *
     * @return array
     */
    public function getUserNames()
    {
        $ret = array();
        $sql = 'SELECT `id`, `username` FROM `'.$this->_database.'`.`'.$this->_tableUsers . '`'
                . ' ORDER BY `username` ASC';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        foreach ($res as $val) {
            $ret[$val['id']] = $val['username'];
        }
        return $ret;
    }

    /**
     * Get a user by id
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
     * Get a user by name
     *
     * @param string $userName Name of user
     *
     * @return array
     */
    public function getUserByName($userName)
    {
        $sql = 'SELECT * FROM `'.$this->_database.'`.`'.$this->_tableUsers . '`'
                .' WHERE `username`= \'' . $this->_dbo->escape($userName) . '\'';
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
     * Get user reference languages as array.
     * Returns active languages only.
     *
     * @return array
     */
    public function getRefLanguages()
    {
        $sql = "SELECT us.`value`
            FROM `{$this->_database}`.`{$this->_tableUsersettings}` us
            LEFT JOIN`{$this->_database}`.`{$this->_tableLanguages}` l ON l.`id` = us.`value`
            WHERE us.`user_id` = '{$this->_userId}' AND us.`setting` = 'referenceLanguage' AND l.`active` = 1
            ORDER BY us.`value` ASC";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        $ret = array();
        if (is_array($res)) {
            foreach ($res as $value) {
                $ret[] = $value['value'];
            }
        }
        return $ret;
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
        $paramPattern = "(%s, '%s', '%s')";
        $insertValues = array();
        foreach ($values as $value) {
            $insertValues[] = sprintf($paramPattern, $this->_userId, $name, $value);
        }
        $params = implode(', ', $insertValues);

        $sql = 'INSERT INTO `'.$this->_tableUsersettings . '`'
                    . ' (`user_id`,`setting`,`value`) VALUES '.$params;
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return $res;
    }

    /**
     * Save language edit rights of a user db
     *
     * @param  int   $userId      The id of the user
     * @param  array $languageIds Array of language ids
     *
     * @return boolean
     */
    public function saveLanguageRights($userId, $languageIds)
    {
        // first remove rights from all other languages
        $sql = 'DELETE FROM `'.$this->_tableUserLanguages . '`'
                    . ' WHERE `user_id` = ' . $userId;
        if (!empty($languageIds)) {
            $sql .= ' AND NOT `language_id` IN (' . implode(',', $languageIds) . ')';
        }
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if ($res === false) {
            return false;
        }

        if (!empty($languageIds)) {
            $sql = 'REPLACE INTO `'.$this->_tableUserLanguages . '`' . ' (`user_id`,`language_id`) VALUES ';
            foreach ($languageIds as $languageId) {
                $sql .= sprintf('(%s, %s), ', (int) $userId, (int) $languageId);
            }
            $sql = substr($sql, 0, -2);
            $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        }
        return $res;
    }

    /**
     * Deletes an user setting.
     *
     * @param string $name Name of setting to delete.
     *
     * @return bool
     */
    public function deleteSetting($name)
    {
        $sqlName = $this->_dbo->escape($name);
        $sql = "DELETE FROM `{$this->_tableUsersettings}`
            WHERE `user_id` = {$this->_userId} AND `setting` = '$sqlName'";
        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        return ($res !== null && $res !== false);
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
        $userId = (int) $userId;
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
     * Get user language edit rights.
     * Needed to get a sortet list by locale.
     *
     * @param int  $userId Id of user, if not set use the current user
     * @param bool $skipInactiveLanguages Only return active languages
     *
     * @return array
     */
    public function getUserLanguageRights($userId = 0, $skipInactiveLanguages = true)
    {
        $userId = (int) $userId;
        if ($userId == 0) {
            $userId = $this->_userId;
        }
        $sql = 'SELECT r.`language_id` FROM `'.$this->_database.'`.`' . $this->_tableUserLanguages . '` r'
                . ' JOIN `'.$this->_database.'`.`' . $this->_tableLanguages . '` l ON '
                . ' l.`id` = r.`language_id`'
                . ' WHERE `user_id`=\''.$userId.'\'';
        if ($skipInactiveLanguages === true) {
            $sql .= ' AND `l`.`active` = 1';
        }
        $sql .= ' ORDER BY `l`.`locale` ASC';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        $ret = array();
        foreach ($res as $val) {
            $ret[] = $val['language_id'];
        }
        return $ret;
    }

    /**
     * Get global rights of given user (all except edit rights of languages)
     *
     * @param int $userId Id of user
     *
     * @return array
     */
    public function getUserGlobalRights($userId = null)
    {
        if ($userId === null) {
            $userId = $this->_userId;
        }
        $sql = 'SELECT * FROM `'.$this->_database.'`.`' . $this->_tableUserrights . '`'
                .' WHERE `user_id`=\''.$userId.'\' AND NOT `right`=\'edit\'';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        $ret = array();
        foreach ($res as $r) {
            $ret[$r['right']] = $r['value'];
        }
        //set defaults
        $defaults = array(
            'addVar' => 0,
            'admin'  => 0,
            'export' => 0,
            'createFile' => 0
        );
        $ret = array_merge($defaults, $ret);
        $this->_userrights = $ret;
        return $this->_userrights;
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

    /**
     * Check the value of a right for the given user
     *
     * @param int    $userId Id of user
     * @param string $right  The right to get
     * @param string $value  The value to check
     *
     * @return false|string
     */
    public function getRight($userId, $right, $value)
    {
        $userId = (int) $userId;
        $value  = (int) $value;
        $sql = 'SELECT `value` FROM `'.$this->_database.'`.`' . $this->_tableUserrights . '`'
                . ' WHERE `user_id`=' . $userId
                . ' AND `right`=\'' . $this->_dbo->escape($right) . '\''
                . ' AND `value`=' . $value;
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        return isset($res[0]) ? $res[0] : false;
    }

    /**
     * Save a user right to database
     *
     * @param int    $userId Id of user
     * @param string $right  Name of right
     * @param string $value  Value to save
     *
     * @return bool
     */
    public function saveRight($userId, $right, $value = "1")
    {
        $this->deleteRight($userId, $right, $value);
        $sql = 'INSERT INTO `'.$this->_database.'`.`' . $this->_tableUserrights . '`'
                .' (`user_id`, `right`, `value`) VALUES ('
                . intval($userId) . ', '
                . '\'' . $this->_dbo->escape($right) . '\', '
               . $value . ') ON DUPLICATE KEY UPDATE `value` = ' . (int) $value;
        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE, false);
        return $res;
    }

    /**
     * Delete a user right from database
     *
     * @param int    $userId Id of user
     * @param string $right  Name of right
     * @param int    $value  The value to delete
     *
     * @return bool
     */
    public function deleteRight($userId, $right, $value)
    {
        // check if user has an entry for this right
        $sql = 'DELETE FROM `'.$this->_database.'`.`' . $this->_tableUserrights . '`'
                .' WHERE `user_id`=' . intval($userId)
                .' AND `right`=\'' . $this->_dbo->escape($right) . '\''
                .' AND `value`=' . intval($value);
        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        return $res;
    }

    /**
     * Create or update a user account
     *
     * @param array $params Parameters of account
     *
     * @return false|int False if there was an error, otherwise return user id
     */
    public function saveAccount($params)
    {
        if ($params['id'] != 0) {
            $sql = 'UPDATE `'.$this->_database.'`.`' . $this->_tableUsers . '`'
                . ' SET `username` = \'' . $this->_dbo->escape($params['user_name']) . '\', '
                . ' `active`=' . intval($params['user_active']);
            if ($params['pass1'] > '') {
                $sql .= ', `password`=MD5(\''. $this->_dbo->escape($params['pass1']) . '\')';
            }
            $sql .= ' WHERE `id`=' . intval($params['id']);
        } else {
            $sql = 'INSERT INTO `'.$this->_database.'`.`' . $this->_tableUsers . '`'
                    . ' (`username`, `password`, `active`) VALUES ('
                    . '\'' . $this->_dbo->escape($params['user_name']) . '\', '
                    . 'MD5(\''. $this->_dbo->escape($params['pass1']) . '\'), '
                    . '`active`=' . intval($params['user_active']) . ')';
        }

        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        if ($res !== false) {
            $user = $this->getUserByName($params['user_name']);
            return isset($user['id']) ? $user['id'] : false;
        }
        return false;
    }
}

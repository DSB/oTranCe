<?php

class Application_Model_LanguageEntries
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
    private $_tableKeys;

    /**
     * Database table containing translations
     * @var string
     */
    private $_tableTranslations;

    /**
     * Database table containing languages
     * @var string
     */
    private $_tableLanguages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_config = Msd_Configuration::getInstance();
        $this->_database = $this->_config->get('config.dbuser.db');
        $this->_tableLanguages = $this->_config->get('config.table.languages');
        $this->_tableTranslations = $this->_config->get('config.table.translations');
        $this->_tableKeys = $this->_config->get('config.table.keys');
        $this->_tableFileTemplates = $this->_config->get('config.table.filetemplates');
        $this->_dbo = Msd_Db::getAdapter();
        $this->_dbo->selectDb($this->_database);
    }

    /**
     * Get the list of available languages as ass. array[id] = array(meta);
     *
     * @param bool $languageNamesOnly If set, only the names are returned
     *
     * @return array
     */
    public function getLanguages($languageNamesOnly = false)
    {
        $sql = 'SELECT * FROM `' . $this->_tableLanguages . '` ORDER BY `locale`';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        $ret = array();
        foreach ($res as $val) {
            if ($languageNamesOnly === true) {
                $ret[$val['id']] = $val['name'];
            } else {
                $ret[$val['id']] = $val;
            }
        }
        return $ret;
    }

    /**
     * Get all language vars of a language and return as ass. array
     *
     * @param string $language
     *
     * @return array
     */
    public function getLanguageKeys($language)
    {
        $ret = array();
        $sql = 'SELECT `key`,`text` FROM `' . $this->_tableTranslations . '`'
               . ' WHERE `locale`= \'' . $language . '\' ORDER BY `keyval` ASC';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        foreach ($res as $data) {
            $val = $this->_dbo->escape($data[$language]);
            $val = $this->_normalize($val);
            $ret[$data['keyval']] = $val;
        }
        return $ret;
    }

    /**
     * Normalize data from database
     *
     * @param string $val
     * @return string
     */
    private function _normalize($val)
    {
        //TODO Use for import afterwards remove
        $search = array(
            "\\\\n",
            "\r\n",
            "\n\r",
            "\r",
            "<br>");
        $replace = array(
            "\n",
            "\n",
            "\n",
            "\n",
            "<br />");
        $val = trim(str_replace($search, $replace, $val));
        $val = wordwrap($val, 38, "\"\n    .\" ");
        return $val;
    }

    /**
     * Get combined status info of all languages
     *
     * @return array
     */
    public function getStatus()
    {
        $ret = array();
        $totalLanguageVars = $this->getNrOfLanguageVars();
        $languages = $this->getLanguages(false);
        $translators = $this->getTranslators();
        $pattern = "SELECT count(*) as anzahl FROM `" . $this->_tableTranslations . "` "
                   . " WHERE `lang_id`= %d AND `text` > ''";
        foreach ($languages as $val) {
            $langId = $val['id'];
            $sql = sprintf($pattern, (int)$val['id']);
            $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
            $translated = $res[0]['anzahl'];
            $ret[$langId] = array();
            $ret[$langId]['notTranslated'] = $totalLanguageVars - $translated;
            $ret[$langId]['translated'] = $translated;
            $percentTranslated = (100 * $translated) / $totalLanguageVars;
            $ret[$langId]['done'] = round($percentTranslated, 2);
            $ret[$langId]['translators'] = '';
            if (isset($translators[$langId])) {
                $ret[$langId]['translators'] = $translators[$langId];
            }
        }
        return $ret;
    }

    /**
     * Get the number of different language variables
     *
     * @return int
     */
    public function getNrOfLanguageVars()
    {
        $sql = 'SELECT count(*) as `nrOfLanguageVars` FROM `' . $this->_tableKeys . '`';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_OBJECT, true);
        return isset($res[0]->nrOfLanguageVars) ? $res[0]->nrOfLanguageVars : 0;
    }

    /**
     * Get list of translators for each language
     *
     * @return array
     */
    public function getTranslators()
    {
        $userModel = new Application_Model_User();
        return $userModel->getTranslators();
    }

    /**
     * Get key ids from database
     *
     * @param string $languages
     * @param string $filter
     * @param int    $offset
     * @param int    $nrOfRecords
     * @param int    $fileTemplateId
     *
     * @return array
     */
    public function getEntries($languages, $filter, $offset = 0, $nrOfRecords = 30, $fileTemplateId = 0)
    {
        if (empty($languages)) {
            return array();
        }

        if ($nrOfRecords < 10) {
            $nrOfRecords = 10;
        }
        //find key ids
        $sql = 'SELECT SQL_CALC_FOUND_ROWS k.`id`,  k.`key`'
               . ' FROM `' . $this->_tableKeys . '` k '
               . ' LEFT JOIN `' . $this->_tableTranslations . '` t ON  k.`id` = t.`key_id`'
               . ' WHERE (t.`lang_id` IN (' . implode(',', $languages) . ') ';
        if ($filter > '') {
            $sql .= ' AND (t.`text` LIKE \'%' . $this->_dbo->escape($filter) . '%\'';
            $sql .= ' OR k.`key` LIKE \'%' . $this->_dbo->escape($filter) . '%\')';
            $sql .= ' OR (k.`key` LIKE \'%' . $this->_dbo->escape($filter) . '%\' AND t.`lang_id` IS NULL))';
        } else {
            $sql .= ' OR t.`lang_id` IS NULL)';
        }
        if ($fileTemplateId > 0) {
            $sql .= ' AND k.`template_id` = ' . $this->_dbo->escape($fileTemplateId);
        }
        $sql .= ' GROUP BY k.`id` ORDER BY k.`key` ASC LIMIT ' . $offset . ', ' . $nrOfRecords;
        $hits = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (!is_array($hits)) {
            return array();
        }
        return $hits;
    }

    /**
     * Get key ids of untranslated variables for given language
     *
     * @param array $languageId Array with all language ids to fetch
     * @param int   $offset
     * @param int   $nrOfRecords
     *
     * @return array
     */
    public function getUntranslated($languageId, $offset = 0, $nrOfRecords = 30)
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS k.`id`,  k.`key`'
               . ' FROM `' . $this->_tableKeys . '` k '
               . ' WHERE NOT EXISTS ('
               . ' SELECT * FROM `' . $this->_tableTranslations . '` t'
               . ' WHERE t.`lang_id` = ' . $languageId
               . ' AND t.`key_id` = k.`id`)'
               . ' ORDER BY k.`key` ASC LIMIT ' . $offset . ', ' . $nrOfRecords;
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

    /**
     * Get translations of given key for given languages
     *
     * Return array(lang_id => array (locale => text)
     *
     * @param int   $id    Id of key
     * @param array $languageIds Ids of languages to fetch
     *
     * @return array
     */
    public function getEntryById($id, $languageIds)
    {
        $languages = implode(',', $languageIds);
        $sql = 'SELECT `lang_id`, `text`'
               . ' FROM `' . $this->_database . '`.`' . $this->_tableTranslations . '`'
               . ' WHERE `key_id`=' . $id . ' AND `lang_id` IN (' . $languages . ')';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (empty($res)) {
            return array();
        }
        $ret = array();
        foreach ($res as $r) {
            $ret[$r['lang_id']] = $r['text'];
        }
        return $ret;
    }

    /**
     * Get translation key
     *
     * @param string $key The key to look for
     *
     * @return bool
     */
    public function getEntryByKey($key)
    {
        $sql = 'SELECT `id` FROM `' . $this->_database . '`.`' . $this->_tableKeys . '`'
               . ' WHERE `key`=\'' . $this->_dbo->escape($key) . '\'';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return isset($res[0]) ? $res[0] : false;
    }

    /**
     * Get translation key
     *
     * @param id $id The id of the key to look for
     *
     * @return bool
     */
    public function getKeyById($id)
    {
        $id = (int)$id;
        $sql = 'SELECT * FROM `' . $this->_database . '`.`' . $this->_tableKeys . '`'
               . ' WHERE `id`=' . $id;
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return isset($res[0]) ? $res[0] : false;
    }

    /**
     * Check if the given key exists
     *
     * @param string $key The key to check
     *
     * @return bool
     */
    public function hasEntryWithKey($key)
    {
        $res = $this->getEntryByKey($key);
        return isset($res['id']) ? true : false;
    }

    /**
     * Create a new key
     *
     * @param string $key The key to create
     *
     * @return bool
     */
    public function saveNewKey($key)
    {
        $sql = 'INSERT INTO `' . $this->_database . '`.`' . $this->_tableKeys . '`'
               . ' SET `key`=\'' . $this->_dbo->escape($key) . '\', '
               . '`dt`=\'' . date('Y-m-d H-i-s', time()) . '\'';

        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        return $res;
    }

    /**
     * Delete all entries in translation table by key of language var
     *
     * @param int $keyId Id of key to be deleted
     *
     * @return bool
     */
    public function deleteEntryByKeyId($keyId)
    {
        $keyId = (int)$keyId;
        $sql = 'DELETE FROM `' . $this->_database . '`.`' . $this->_tableTranslations . '`'
               . ' WHERE `key_id`= \'' . $this->_dbo->escape($keyId) . '\'';
        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);

        $sql = 'DELETE FROM `' . $this->_database . '`.`' . $this->_tableKeys . '`'
               . ' WHERE `id` = ' . $keyId;
        $res &= $this->_dbo->query($sql, Msd_Db::SIMPLE);
        return $res;
    }

    /**
     * Save values to database.
     *
     * @param int   $keyId
     * @param array $newValues
     *
     * @return bool|string
     */
    public function saveEntries($keyId, $newValues)
    {
        $oldValues = $this->getEntryById($keyId, array_keys($newValues), true);
        foreach ($newValues as $lang => $text) {
            $text = $this->_dbo->escape($text);
            $date = date('Y-m-d H:i:s', time());
            $sql = 'INSERT INTO `' . $this->_database . '`.`' . $this->_tableTranslations . '` '
                    . ' (`lang_id`, `key_id`, `text`, `dt`) VALUES ('
                    . $lang . ', ' . $keyId . ', \'' . $text . '\', \'' . $date . '\')'
                   . ' ON DUPLICATE KEY UPDATE `text`= \'' . $text . '\', `dt` = \'' . $date . '\'';
            try {
                $this->_dbo->query($sql, Msd_Db::SIMPLE);
            } catch (Msd_Exception $e) {
                return $e->getMessage();
            }
        }
        // log changes all at once
        foreach ($newValues as $lang => $text) {
            if (!isset($oldValues[$lang])) {
                $oldValue[$lang] = '';
            }
        }
        $historyModel = new Application_Model_History();
        $historyModel->logChanges($keyId, $oldValues, $newValues);
        return true;
    }

    /**
     * Retrieves the file template, which is assigned to language variable.
     *
     * @param string $keyId ID of the language variable.
     *
     * @return array ID of the assigned template if exists, "empty" array otherwise.
     */
    public function getAssignedFileTemplate($keyId)
    {
        $keyId = $this->_dbo->escape($keyId);
        $sql = "SELECT ft.`id`, ft.`name`, ft.`filename`
            FROM `{$this->_database}`.`{$this->_tableKeys}` k
            LEFT JOIN `{$this->_database}`.`{$this->_tableFileTemplates}` ft ON ft.`id` = k.`template_id`
            WHERE k.`id` = '$keyId'";
        $result = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return isset($result[0]) ? $result[0] : array('id' => 0);
    }

    /**
     * Assigns a language variable to a file template,
     *
     * @param string $keyId      ID of the language variable.
     * @param string $templateId ID of the template to assign.
     *
     * @return bool|string Returns TRUE on success, otherwise returns the error message.
     */
    public function assignFileTemplate($keyId, $templateId)
    {
        $sql = "UPDATE `{$this->_database}`.`{$this->_tableKeys}`
            SET `template_id` = '$templateId'
            WHERE `id` = '$keyId'";
        try {
            $this->_dbo->query($sql, Msd_Db::SIMPLE);
        } catch (Msd_Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
}

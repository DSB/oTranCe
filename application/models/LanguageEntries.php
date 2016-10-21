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
 * Language entries model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_LanguageEntries extends Msd_Application_Model
{
    /**
     * Database table containing language var keys
     *
     * @var string
     */
    private $_tableKeys;

    /**
     * Database table containing translations
     *
     * @var string
     */
    private $_tableTranslations;

    /**
     * Database table containing languages
     *
     * @var string
     */
    private $_tableLanguages;

    /**
     * Database table containing file tmeplates
     *
     * @var string
     */
    private $_tableFileTemplates;

    /**
     * Number of found rows for SQL_CALC_FOUND_ROWS keyword.
     *
     * @var int
     */
    private $_foundRows = null;

    /**
     * Sotres the validation messages.
     *
     * @var array
     */
    private $_validateMessages = array();

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $this->_tableLanguages     = $this->_tablePrefix . 'languages';
        $this->_tableTranslations  = $this->_tablePrefix . 'translations';
        $this->_tableKeys          = $this->_tablePrefix . 'keys';
        $this->_tableFileTemplates = $this->_tablePrefix . 'filetemplates';
    }

    /**
     * Get all keys
     *
     * @return array
     */
    public function getAllKeys()
    {
        $sql = "SELECT `id`, `key`,`template_id` FROM `{$this->_tableKeys}` "
            . " ORDER BY `template_id` ASC, `key` ASC";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        $ret = array();
        foreach ($res as $data) {
            $ret[$data['id']] = array(
                'templateId' => $data['template_id'],
                'key'        => $data['key']
            );
        }

        return $ret;
    }

    /**
     * Get all language vars of a language and return as ass. array
     *
     * @param int $languageId Id of language to fetch
     *
     * @return array
     */
    public function getTranslations($languageId)
    {
        $ret = array();
        $sql = "SELECT `key_id`, `text` FROM `{$this->_tableTranslations}`
              WHERE `lang_id`= " . intval($languageId);
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        foreach ($res as $data) {
            $ret[$data['key_id']] = $data['text'];
        }

        return $ret;
    }

    /**
     * Get combined status info of all languages
     *
     * @param array $languageIds Array with all active languages.
     *
     * @return array
     */
    public function getStatus($languageIds)
    {
        $ret               = array();
        $totalLanguageVars = $this->getNrOfLanguageVars();
        $translators       = $this->getTranslators();
        $pattern
                           =
            "SELECT count(*) as anzahl, SUM(`needs_update`) as review FROM `" . $this->_tableTranslations . "` "
            . " INNER JOIN `" . $this->_tableKeys . "` "
            . "ON (`" . $this->_tableTranslations . "`.`key_id` = `" . $this->_tableKeys . "`.`id` AND `" . $this->_tableKeys . "`.clf = 0) "
            . " WHERE `lang_id`= %d";
        foreach ($languageIds as $val) {
            $langId                        = $val['id'];
            $sql                           = sprintf($pattern, (int)$val['id']);
            $res                           = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
            $translated                    = $res[0]['anzahl'];
            $ret[$langId]                  = array();
            $ret[$langId]['languageId']    = $langId;
            $ret[$langId]['notTranslated'] = $totalLanguageVars - $translated;
            $ret[$langId]['translated']    = $translated;
            $ret[$langId]['review']        = $res[0]['review'];
            $percentTranslated             = 0;
            if ($totalLanguageVars > 0) {
                $percentTranslated = (100 * $translated) / $totalLanguageVars;
            }
            $ret[$langId]['done']        = round($percentTranslated, 2);
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
        $sql = 'SELECT count(*) as `nrOfLanguageVars` FROM `' . $this->_tableKeys . '` WHERE `clf` = 0';
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
     * Search for term in translations table.
     *
     * Saves number of hits in $this->_foundRows.
     *
     * @param string $languageIds    Ids of languages to search in
     * @param string $searchphrase   Text to find
     * @param int    $offset         Number of records to skip
     * @param int    $nrOfRecords    Number of hits to return
     * @param int    $fileTemplateId If set, only search in this template
     *
     * @return array
     */
    public function getEntriesByValue($languageIds, $searchphrase, $offset = 0, $nrOfRecords = 30, $fileTemplateId = 0)
    {
        if (empty($languageIds)) {
            return array();
        }

        //find key ids
        $sql   = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT t.`key_id` FROM `' . $this->_tableTranslations . '` t ';
        $where = array();
        $join  = '';
        if ($searchphrase > '') {
            $where[] = 't.`text` LIKE \'%' . $this->_dbo->escape($searchphrase) . '%\' AND '
                . 't.`lang_id` IN (' . implode(",", $languageIds) . ')';
        }

        // if hits are filtered by a file template, we need to join the key table here
        if ($fileTemplateId > 0) {
            $join    = ' JOIN `' . $this->_tableKeys . '` k ON k.`id` = t.`key_id` ';
            $where[] = 'k.`template_id` = ' . $this->_dbo->escape($fileTemplateId);
        }
        if ($join > '') {
            $sql .= $join;
        }
        if (count($where) > 0) {
            $sql .= ' WHERE (' . implode(') AND (', $where) . ')';
        }
        $sql .= ' ORDER BY t.`key_id` ASC LIMIT ' . $offset . ', ' . $nrOfRecords;
        $rawKeyIds = $this->_dbo->query($sql, Msd_Db::ARRAY_NUMERIC);
        $this->setRowCount();
        if ($this->_foundRows == 0) {
            return array();
        }
        $keyIds = array();
        foreach ($rawKeyIds as $rawKeyId) {
            $keyIds[] = $rawKeyId[0];
        }
        if (empty($keyIds)) {
            return array();
        }
        $sql  = 'SELECT `id`,  `key`, `template_id` FROM `' . $this->_tableKeys . '` '
            . 'WHERE `id` IN (' . implode(',', $keyIds) . ') ORDER BY `key` ASC';
        $hits = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        return is_array($hits) ? $hits : array();
    }

    /**
     * Search for term in keys table.
     *
     * @param string $searchphrase   Text to search for
     * @param int    $offset         Number of records to skip
     * @param int    $nrOfRecords    Number of hits to return
     * @param int    $fileTemplateId If set, only search in this template
     *
     * @return array
     */
    public function getEntriesByKey($searchphrase, $offset = 0, $nrOfRecords = 30, $fileTemplateId = 0)
    {
        //find key ids
        $sql   = 'SELECT SQL_CALC_FOUND_ROWS k.`id`,  k.`key`, k.`template_id`'
            . ' FROM `' . $this->_tableKeys . '` k ';
        $where = array();
        if ($searchphrase > '') {
            $where[] = 'k.`key` LIKE \'%' . $this->_dbo->escape($searchphrase) . '%\'';
        }
        if ($fileTemplateId > 0) {
            $where[] .= 'k.`template_id` = ' . $this->_dbo->escape($fileTemplateId);
        }
        if (count($where) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' GROUP BY k.`id` ORDER BY k.`key` ASC LIMIT ' . $offset . ', ' . $nrOfRecords;

        $hits = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        return is_array($hits) ? $hits : array();
    }

    /**
     * Get key ids of untranslated variables for given language
     *
     * @param int    $languageId   If set, only search in this language
     * @param string $searchphrase Phrase to search
     * @param int    $offset       Number of records to skip
     * @param int    $nrOfRecords  Number of hits to return
     * @param int    $templateId   If set, only search in this template
     *
     * @return array
     */
    public function getUntranslated(
        $languageId = 0,
        $searchphrase = '',
        $offset = 0,
        $nrOfRecords = 30,
        $templateId = 0
    ) {
        $this->_foundRows = null;
        $sql              = 'SELECT SQL_CALC_FOUND_ROWS k.`id`,  k.`key`, k.`template_id`'
            . ' FROM `' . $this->_tableKeys . '` k ';

        $sql .= ' LEFT JOIN `' . $this->_tableTranslations . '` t'
            . ' ON t.`key_id` = k.`id`';

        $where = array();

        if ($searchphrase > '') {
            $where[] = 'k.`key` LIKE \'%' . $this->_dbo->escape($searchphrase) . '%\'';
        }

        if ($templateId > 0) {
            $where[] = 'k.`template_id` = ' . intval($templateId);
        }

        if ($languageId > 0) {
            // we are looking for a specific language
            // Add the language condition to the JOIN, not to the WHERE clause.
            $sql .= ' AND t.`lang_id`=' . $languageId
                . ' WHERE (t.`text`=\'\' OR t.`text` IS NULL OR t.`needs_update`=1)';
        } else {
            // find all untranslated keys
            $sql .= ' WHERE (t.`text`=\'\' OR t.`text` IS NULL OR t.`needs_update`=1)';
        }

        if (!empty($where)) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY k.`key` ASC ';
        if ($nrOfRecords > 0) {
            $sql .= ' LIMIT ' . $offset . ', ' . $nrOfRecords;
        }
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        return $res;
    }

    /**
     * Get the key id of untranslated key in given languages
     *
     * @param int $languageId Id of languages to search in
     * @param int $offset     Skipped entries
     *
     * @return null|int
     */
    public function getUntranslatedKey($languageId, $offset = 0)
    {
        if ($offset < 0) {
            $offset = 0;
        }
        $sql = 'SELECT k.`id`, t.`lang_id`, t.`text` FROM `' . $this->_tableKeys . '` k '
            . ' LEFT JOIN `' . $this->_tableTranslations . '` t'
            . ' ON t.`key_id` = k.`id`'
            . ' AND t.`lang_id` = ' . $languageId
            . ' WHERE (t.`text`=\'\' OR t.`text` IS NULL)'
            . ' ORDER BY k.`key` ASC LIMIT ' . $offset . ', 1';

        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (isset($res[0]['id'])) {
            return $res[0]['id'];
        }

        return null;
    }

    /**
     * Save nr of rows to property.
     *
     * Query must be invoked using SQL_CALC_FOUND_ROWS and method needs to be called right after the query is executed.
     *
     * @return integer
     */
    public function setRowCount()
    {
        $this->_foundRows = $this->_dbo->getRowCount();
    }

    /**
     * Get translations of given key for given languages
     *
     * Return array(lang_id => array (locale => text)
     *
     * @param int       $id          Id of key
     * @param int|array $languageIds Id(s) of languages to fetch
     *
     * @return array
     */
    public function getTranslationsByKeyId($id, $languageIds)
    {
        $id  = (int)$id;
        $ret = array();
        if (!is_array($languageIds)) {
            $languageIds = (array)$languageIds;
        }
        if ($id == 0 || empty($languageIds)) {
            return $ret;
        }
        $languages = implode(',', $languageIds);
        $sql       = 'SELECT `lang_id`, `text`'
            . ' FROM `' . $this->_database . '`.`' . $this->_tableTranslations . '`'
            . ' WHERE `key_id`=' . $id . ' AND `lang_id` IN (' . $languages . ')';
        $res       = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (empty($res)) {
            return array();
        }
        foreach ($res as $r) {
            $ret[$r['lang_id']] = $r['text'];
        }

        return $ret;
    }

    /**
     * Get the needs update status for all languages for the given key
     *
     * Return array(lang_id => boolean)
     *
     * @param int $id Id of key
     *
     * @return array
     */
    public function getNeedsUpdateStatusByKeyId($id)
    {
        $sql    = "SELECT `lang_id`, `needs_update` "
            . "FROM `{$this->_database}`.`{$this->_tableTranslations}` "
            . "WHERE `key_id`='$id'";
        $result = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        $return = array();
        foreach ($result as $row) {
            $return[$row['lang_id']] = $row['needs_update'] == '1';
        }

        return $return;
    }

    /**
     * Get translations of given keys for given languages
     *
     * Return array(lang_id => array (locale => text)
     *
     * @param array $keys       Ids of keys to fetch
     * @param int   $templateId Id of the file template
     * @param int   $languageId Id of the language to fetch
     *
     * @return array
     */
    public function getEntriesByKeys($keys, $templateId, $languageId)
    {
        $ret = array();
        foreach ($keys as $k => $v) {
            $keys[$k] = $this->_dbo->escape($v);
        }
        $sql = 'SELECT k.`key`, t.`text` FROM `' . $this->_database . '`.`' . $this->_tableKeys . '` k'
            . ' LEFT JOIN `' . $this->_database . '`.`' . $this->_tableTranslations . '` t'
            . ' ON t.`key_id` = k.`id`'
            . ' WHERE k.`key` IN (\'' . implode('\',\'', $keys) . '\') '
            . ' AND k.`template_id` = ' . (int)$templateId
            . ' AND t.`lang_id` = ' . (int)$languageId;
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        foreach ($res as $r) {
            $ret[$r['key']] = $r['text'];
        }

        foreach ($keys as $key) {
            if (!isset($ret[$key])) {
                $ret[$key] = '';
            }
        }

        return $ret;
    }

    /**
     * Get ids of given keys
     *
     * @param array $keys Ids of keys to fetch
     *
     * @return array
     */
    public function getIdsByKeys($keys)
    {
        $ret = array();
        foreach ($keys as $k => $v) {
            $keys[$k] = $this->_dbo->escape($v);
        }
        $sql = 'SELECT * FROM `' . $this->_database . '`.`' . $this->_tableKeys . '` k'
            . ' WHERE `key` IN (\'' . implode('\',\'', $keys) . '\') ORDER BY `key`';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        foreach ($res as $r) {
            $ret[$r['id']] = $r;
        }

        return $ret;
    }

    /**
     * Add translations for the given languages to the entries.
     *
     * @param array $languageIds Ids of languages
     * @param array $entries     Entries to add
     *
     * @return array
     */
    public function assignTranslations($languageIds, $entries)
    {
        if (empty($languageIds) || empty($entries)) {
            return array();
        }

        $result = array();
        $keyIds = array();
        foreach ($entries as $entry) {
            $keyId          = $entry['id'];
            $keyIds[]       = $keyId;
            $result[$keyId] = $entry;
        }

        $sql          = 'SELECT `key_id`, `lang_id`, `text`, `needs_update` FROM `' . $this->_tableTranslations
            . '` WHERE `key_id` IN (' . implode(',', $keyIds) . ') AND `lang_id` IN ('
            . implode(',', $languageIds) . ')';
        $translations = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        foreach ($translations as $translation) {
            $keyId  = $translation['key_id'];
            $langId = $translation['lang_id'];
            if (!isset($result[$keyId]['languages'])) {
                $result[$keyId]['languages'] = array();
            }
            $result[$keyId]['languages'][$langId]   = $translation['text'];
            $result[$keyId]['needsUpdate'][$langId] = $translation['needs_update'] == 1;
        }

        return $result;
    }

    /**
     * Get translation key
     *
     * @param string $key          The key to look for
     * @param int    $fileTemplate Id of file template
     *
     * @return bool
     */
    public function getEntryByKey($key, $fileTemplate = 0)
    {
        $sql = 'SELECT `id` FROM `' . $this->_database . '`.`' . $this->_tableKeys . '`'
            . ' WHERE `key`=\'' . $this->_dbo->escape($key) . '\''
            . ' AND `template_id` = ' . (int)$fileTemplate;
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        return isset($res[0]) ? $res[0] : false;
    }

    /**
     * Get translation key
     *
     * @param int $id The id of the key to look for
     *
     * @return bool
     */
    public function getKeyById($id)
    {
        $id  = (int)$id;
        $sql = 'SELECT * FROM `' . $this->_database . '`.`' . $this->_tableKeys . '`'
            . ' WHERE `id`=' . $id;
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        return isset($res[0]) ? $res[0] : false;
    }

    /**
     * Check if the given key exists
     *
     * @param string $key          The key to check
     * @param int    $fileTemplate Id of file template
     *
     * @return bool
     */
    public function hasEntryWithKey($key, $fileTemplate = 0)
    {
        $res = $this->getEntryByKey($key, $fileTemplate);

        return isset($res['id']) ? true : false;
    }

    /**
     * Create a new key
     *
     * @param string $key        The key to create
     * @param int    $templateId ID of file template
     *
     * @return bool
     */
    public function saveNewKey($key, $templateId)
    {
        $sql = 'INSERT INTO `' . $this->_database . '`.`' . $this->_tableKeys . '`'
            . ' SET `key`=\'' . $this->_dbo->escape($key) . '\', '
            . '`dt`=\'' . date('Y-m-d H-i-s', time()) . '\', '
            . '`template_id`=' . intval($templateId);
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
        $sql   = 'DELETE FROM `' . $this->_database . '`.`' . $this->_tableTranslations . '`'
            . ' WHERE `key_id`= \'' . $this->_dbo->escape($keyId) . '\'';
        $res   = $this->_dbo->query($sql, Msd_Db::SIMPLE);

        $sql = 'DELETE FROM `' . $this->_database . '`.`' . $this->_tableKeys . '`'
            . ' WHERE `id` = ' . $keyId;
        $res &= $this->_dbo->query($sql, Msd_Db::SIMPLE);

        return (bool)$res;
    }

    /**
     * Save values to database.
     *
     * @param int   $keyId              Id of key
     * @param array $newValues          Translations
     * @param int   $fallbackLanguageId Id of fallback language
     * @param bool  $ignoreSmallChange  Whether to flag other languages as "please re-check"
     *
     * @return bool|string
     */
    public function saveEntries($keyId, $newValues, $fallbackLanguageId = null, $ignoreSmallChange = false)
    {
        $keyId     = (int)$keyId;
        $oldValues = $this->getTranslationsByKeyId($keyId, array_keys($newValues), true);
        // remove unchanged languages
        foreach ($newValues as $langId => $newValue) {
            if (isset($oldValues[$langId]) && trim($oldValues[$langId]) == trim($newValue)) {
                unset($newValues[$langId]);
            }

            if (!isset($oldValues[$langId]) && trim($newValue) == '') {
                unset($newValues[$langId]);
            }

        }
        if (empty($newValues)) {
            //nothing changed == nothing to do -> return
            return true;
        }

        // save changes to database
        $changedLanguageIds = array();
        foreach ($newValues as $langId => $text) {
            $langId               = (int)$langId;
            $changedLanguageIds[] = $langId;
            $text                 = $this->_dbo->escape($text);
            $date                 = date('Y-m-d H:i:s', time());
            $sql                  = 'INSERT INTO `' . $this->_database . '`.`' . $this->_tableTranslations . '` '
                . ' (`lang_id`, `key_id`, `text`, `dt`) VALUES ('
                . $langId . ', ' . $keyId . ', \'' . $text . '\', \'' . $date . '\')'
                . ' ON DUPLICATE KEY UPDATE `text`= \'' . $text . '\', `dt` = \'' . $date . '\''
                . ', `needs_update`=\'0\'';

            try {
                $this->_dbo->query($sql, Msd_Db::SIMPLE);
            } catch (Msd_Exception $e) {
                return $e->getMessage();
            }
        }

        if (!$ignoreSmallChange && $fallbackLanguageId != null && array_key_exists($fallbackLanguageId, $newValues)) {
            $sql = "UPDATE `{$this->_database}`.`{$this->_tableTranslations}` "
                . "SET needs_update=1 "
                . "WHERE `key_id`='{$keyId}' AND `lang_id` NOT IN (" . implode(',', $changedLanguageIds) . ")";
            $this->_dbo->query($sql);
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
        $keyId  = $this->_dbo->escape($keyId);
        $sql    = "SELECT ft.`id`, ft.`name`, ft.`filename`
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

    /**
     * Delete all translations of the given language
     *
     * @param int $languageId Id of language
     *
     * @return bool
     */
    public function deleteLanguageEntries($languageId)
    {
        $sql = 'DELETE FROM `' . $this->_database . '`.`' . $this->_tableTranslations . '`'
            . ' WHERE `lang_id` = ' . intval($languageId);

        return (bool)$this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Update the name of a key.
     *
     * @param int    $keyId   Key-Id to update
     * @param string $keyName New key name
     *
     * @return bool
     */
    public function updateKeyName($keyId, $keyName)
    {
        $sql = 'UPDATE `' . $this->_database . '`.`' . $this->_tableKeys . '`'
            . ' SET `key` = \'' . $this->_dbo->escape($keyName) . '\''
            . ' WHERE `id` = ' . $keyId;
        $res = $this->_dbo->query($sql, Msd_db::SIMPLE);
        if ($res !== false) {
            // update timestamp of translations to make them being exported
            $sql = 'UPDATE `' . $this->_database . '`.`' . $this->_tableTranslations . '`'
                . ' SET `dt` = NOW() WHERE `key_id` = ' . $keyId;
            $res &= $this->_dbo->query($sql, Msd_db::SIMPLE);
        }

        return (bool)$res;
    }

    /**
     * Validates the given language key.
     *
     * @param string $keyName      Name of the language key to validate.
     * @param int    $fileTemplate ID of the file template.
     *
     * @return bool
     */
    public function validateLanguageKey($keyName, $fileTemplate)
    {
        $this->_validateMessages = array();
        $translator              = Msd_Language::getInstance()->getTranslator();

        // check for min-length of 1 character
        if (strlen($keyName) < 1) {
            $this->_validateMessages[] = $translator->translate('L_VALIDATE_ERROR_NAME_TOO_SHORT');

            return false;
        }

        // check if we already have a lang var with that name
        if ($this->hasEntryWithKey($keyName, $fileTemplate)) {
            $this->_validateMessages[] = sprintf($translator->translate('L_VALIDATE_ERROR_KEY_EXISTS'), $keyName);

            return false;
        }

        return true;
    }

    /**
     * Retrieves the validation result messages.
     *
     * @return array
     */
    public function getValidateMessages()
    {
        return $this->_validateMessages;
    }

    /**
     * Removes the needs update flag for the given key and language
     *
     * @param int $languageId Id of the language.
     * @param int $keyId      Id of the key.
     *
     * @return bool
     */
    public function removeNeedsUpdateFlag($languageId, $keyId)
    {
        $sql = "UPDATE `{$this->_database}`.`{$this->_tableTranslations}` "
            . "SET `needs_update`='0' "
            . "WHERE `lang_id`='$languageId' AND `key_id`='$keyId';";

        return (bool)$this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Update the flag for certain language only
     *
     * @param int $keyId        Id of the key.
     * @param bool $value       Value of ind field.
     *
     * @return bool
     */
    public function setOnlyCertainLanguageFlag($keyId, $value) {
            $sql = "UPDATE `{$this->_database}`.`{$this->_tableKeys}` "
                    . "SET `clf`='" . (int) $value . "' "
                    . "WHERE `id`='$keyId';";

            return (bool)$this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

}

<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Import Controller
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_LanguageEntries extends Msd_Application_Model
{
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
     * Database table containing file tmeplates
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
        $tableConfig = $this->_config->getParam('table');
        $this->_tableLanguages = $tableConfig['languages'];
        $this->_tableTranslations = $tableConfig['translations'];
        $this->_tableKeys = $tableConfig['keys'];
        $this->_tableFileTemplates = $tableConfig['filetemplates'];
    }

    /**
     * Get all keys
     *
     * @return array
     */
    public function getAllKeys()
    {
        $sql = "SELECT `id`, `key`,`template_id` FROM `{$this->_tableKeys}` "
                ." ORDER BY `template_id` ASC, `key` ASC";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        $ret = array();
        foreach ($res as $data) {
            $ret[$data['id']] = array(
                'templateId' => $data['template_id'],
                'key'         => $data['key']
            );
        }
        return $ret;
    }

    /**
     * Get all language vars of a language and return as ass. array
     *
     * @param int   $languageId Id of language to fetch
     *
     * @return array
     */
    public function getTranslations($languageId)
    {
        $ret = array();
        $sql = "SELECT `key_id`, `text` FROM `{$this->_tableTranslations}`
              WHERE `lang_id`= ". intval($languageId);
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        foreach ($res as $data) {
            $ret[$data['key_id']] = $data['text'];
        }
        return $ret;
    }

    /**
     * Get combined status info of all languages
     *
     * @param array $languages Array with all active languages.
     *
     * @return array
     */
    public function getStatus($languages)
    {
        $ret = array();
        $totalLanguageVars = $this->getNrOfLanguageVars();
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
            $percentTranslated = 0;
            if ($totalLanguageVars > 0) {
                $percentTranslated = (100 * $translated) / $totalLanguageVars;
            }
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
     * Search for term in translations table.
     *
     * @param string $languages
     * @param string $filter
     * @param int    $offset
     * @param int    $nrOfRecords
     *
     * @return array
     */
    public function getEntriesByValue($languages, $filter, $offset = 0, $nrOfRecords = 30)
    {
        if (empty($languages)) {
            return array();
        }

        if ($nrOfRecords < 10) {
            $nrOfRecords = 10;
        }
        $this->_foundRows = null;
        //find key ids
        $sql = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT t.`key_id` FROM `' . $this->_tableTranslations . '` t ';
        if ($filter > '') {
            $sql .= ' WHERE t.`text` LIKE \'%' . $this->_dbo->escape($filter) . '%\' AND '
                . 't.`lang_id` IN (' . implode(",", $languages) . ')';
        }
        $sql .= ' ORDER BY t.`key_id` ASC LIMIT ' . $offset . ', ' . $nrOfRecords;
        $rawKeyIds = $this->_dbo->query($sql, Msd_Db::ARRAY_NUMERIC);
        $this->_foundRows = $this->getRowCount();
        if ($this->_foundRows == 0) {
            return array();
        }
        $keyIds = array();
        foreach ($rawKeyIds as $rawKeyId) {
            $keyIds[] = $rawKeyId[0];
        }
        $sql = 'SELECT `id`,  `key`, `template_id` FROM `' . $this->_tableKeys . '` '
            . 'WHERE `id` IN (' . implode(',', $keyIds) . ') ORDER BY `key` ASC';
        $hits = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return is_array($hits) ? $hits : array();
    }

    /**
     * Search for term in keys table.
     *
     * @param string $filter
     * @param int    $offset
     * @param int    $nrOfRecords
     * @param int    $fileTemplateId
     *
     * @return array
     */
    public function getEntriesByKey($filter, $offset = 0, $nrOfRecords = 30, $fileTemplateId = 0)
    {
        //find key ids
        $sql = 'SELECT SQL_CALC_FOUND_ROWS k.`id`,  k.`key`, k.`template_id`'
               . ' FROM `' . $this->_tableKeys . '` k ';
        $where = array();
        if ($filter > '') {
            $where[] = 'k.`key` LIKE \'%' . $this->_dbo->escape($filter) . '%\'';
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
     * @param int    $languageId ID of language
     * @param string $filter     Filter for key
     * @param int    $offset
     * @param int    $nrOfRecords
     * @param int    $templateId
     *
     * @return array
     */
    public function getUntranslated($languageId = 0, $filter = '', $offset = 0, $nrOfRecords = 30, $templateId = 0)
    {
        $this->_foundRows = null;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS k.`id`,  k.`key`, k.`template_id`'
               . ' FROM `' . $this->_tableKeys . '` k ';

        $sql .= ' LEFT JOIN `' . $this->_tableTranslations . '` t'
               . ' ON t.`key_id` = k.`id`';

        $where = array();

        if ($filter > '' ) {
            $where[] = 'k.`key` LIKE \'%' . $this->_dbo->escape($filter) . '%\'';
        }

        if ($templateId > 0) {
            $where[] = 'k.`template_id` = '. intval($templateId);
        }

        if ($languageId > 0) {
            // we are looking for a specific language
            // Add the language condition to the JOIN, not to the WHERE clause.
            $sql .= ' AND t.`lang_id`=' . $languageId.' WHERE (t.`text`=\'\' OR t.`text` IS NULL)';
        } else {
            // find all untranslated keys
            $sql .= ' WHERE (t.`text`=\'\' OR t.`text` IS NULL)';
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
     * Get the key id of the first untranslated variables in given languages
     *
     * @param int $languageId ID of languages to search in
     *
     * @return null|int
     */
    public function getFirstUntranslated($languageId)
    {
        $sql = 'SELECT k.`id`, t.`lang_id`, t.`text` FROM `' . $this->_tableKeys . '` k '
                . ' LEFT JOIN `' . $this->_tableTranslations . '` t'
                . ' ON t.`key_id` = k.`id`'
                . ' AND t.`lang_id` = ' . $languageId
                . ' WHERE (t.`text`=\'\' OR t.`text` IS NULL)'
                . ' ORDER BY k.`key` ASC LIMIT 0, 1';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (isset($res[0]['id'])) {
            return $res[0]['id'];
        }
        return null;
    }

    /**
     * Get nr of rows of last query (needs to invoked using SQL_CALC_FOUND_ROWS)
     *
     * @return integer
     */
    public function getRowCount()
    {
        if ($this->_foundRows === null) {
            $this->_foundRows = $this->_dbo->getRowCount();
        }
        return $this->_foundRows;
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
    public function getEntryById($id, $languageIds)
    {
        $id = (int) $id;
        $ret = array();
        if (!is_array($languageIds)) {
            $languageIds = (array) $languageIds;
        }
        if ($id == 0 || empty($languageIds)) {
            return $ret;
        }
        $languages = implode(',', $languageIds);
        $sql = 'SELECT `lang_id`, `text`'
               . ' FROM `' . $this->_database . '`.`' . $this->_tableTranslations . '`'
               . ' WHERE `key_id`=' . $id . ' AND `lang_id` IN (' . $languages . ')';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (empty($res)) {
            return array();
        }
        foreach ($res as $r) {
            $ret[$r['lang_id']] = $r['text'];
        }
        return $ret;
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
                . ' AND k.`template_id` = ' . (int) $templateId
                . ' AND t.`lang_id` = ' . (int) $languageId;
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
     * Add translations for the given languages to the entries.
     *
     * @param array $languageIds
     * @param array $entries
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
            $keyId = $entry['id'];
            $keyIds[] = $keyId;
            $result[$keyId] = $entry;
        }

        $sql = 'SELECT `key_id`, `lang_id`, `text` FROM `' . $this->_tableTranslations . '` WHERE '
            . '`key_id` IN (' . implode(',', $keyIds) . ') AND `lang_id` IN (' . implode(',', $languageIds) . ')';
        $translations = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        foreach ($translations as $translation) {
            $keyId = $translation['key_id'];
            $langId = $translation['lang_id'];
            if (!isset($result[$keyId]['languages'])) {
                $result[$keyId]['languages'] = array();
            }
            $result[$keyId]['languages'][$langId] = $translation['text'];
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
               . ' AND `template_id` = '. (int) $fileTemplate;
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
        $id = (int)$id;
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
        $keyId = (int) $keyId;
        $oldValues = $this->getEntryById($keyId, array_keys($newValues), true);
        // remove unchanged languages
        foreach ($oldValues as $langId => $oldValue) {
            if ($newValues[$langId] == $oldValue) {
                unset($newValues[$langId]);
            }
        }
        if (empty($newValues)) {
            //nothing changed == nothing to do -> return
            return true;
        }

        // save changes to database
        foreach ($newValues as $langId => $text) {
            $langId = (int) $langId;
            $text = $this->_dbo->escape($text);
            $date = date('Y-m-d H:i:s', time());
            $sql = 'INSERT INTO `' . $this->_database . '`.`' . $this->_tableTranslations . '` '
                    . ' (`lang_id`, `key_id`, `text`, `dt`) VALUES ('
                    . $langId . ', ' . $keyId . ', \'' . $text . '\', \'' . $date . '\')'
                   . ' ON DUPLICATE KEY UPDATE `text`= \'' . $text . '\', `dt` = \'' . $date . '\'';
            try {
                $this->_dbo->query($sql, Msd_Db::SIMPLE);
            } catch (Msd_Exception $e) {
                return $e->getMessage();
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

    /**
     * Delete all translations of the given language
     *
     * @param int $languageId Id of language
     *
     * @return bool
     */
    public function deleteLanguageEntries($languageId)
    {
        $sql = 'DELETE FROM `'. $this->_database . '`.`' . $this->_tableTranslations . '`'
            . ' WHERE `lang_id` = ' . intval($languageId);
        return (bool) $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Update the name of a key
     *
     * @param int    $keyId
     * @param string $keyName
     *
     * @return bool
     */
    public function updateKeyName($keyId, $keyName)
    {
        $sql = 'UPDATE `'. $this->_database . '`.`' . $this->_tableKeys . '`'
            . ' SET `key` = \'' . $this->_dbo->escape($keyName) . '\''
            . ' WHERE `id` = '. $keyId;
        return (bool) $this->_dbo->query($sql, Msd_db::SIMPLE);
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
        if (strlen($keyName) < 1) {
            $this->_validateMessages[] = 'Name is too short.';
            return false;
        }

        $pattern = '/^[^A-Z_]*$/';
        if (!preg_match($pattern, $keyName)) {
            $this->_validateMessages[] = 'Name contains illegal characters.<br />'
                       . 'Only "A-Z" and "_" is allowed.';
            return false;
        }

        // check if we already have a lang var with that name
        if ($this->hasEntryWithKey($keyName, $fileTemplate)) {
            $this->_validateMessages[] = 'A language variable with this name already exists in this file template!';
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
}

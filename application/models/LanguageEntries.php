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
     * Get all language vars of a language and return as ass. array
     *
     * @param string $language
     *
     * @return array
     */
    public function getLanguageKeys($language)
    {
        $ret = array();
        $sql = "SELECT k.`key`, t.`text`, k.`template_id` FROM `{$this->_tableTranslations}` t
            LEFT JOIN `{$this->_tableKeys}` k ON k.`id` = t.`key_id`
            WHERE t.`lang_id`= '{$language}' ORDER BY k.`key` ASC";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);
        foreach ($res as $data) {
            $val = $data['text'];
            //$val = $this->_normalize($val);
            $ret[$data['key']] = array('text' => $val, 'templateId' => $data['template_id']);
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
        //TODO Make normalizing configurable for each project
        // disabled for the moment
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
            if ($totalLanguageVars > 0) {
                $percentTranslated = (100 * $translated) / $totalLanguageVars;
            } else {
                $percentTranslated = 0;
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
        $sql = 'SELECT SQL_CALC_FOUND_ROWS k.`id`,  k.`key`, k.`template_id`'
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

        // log changes
        foreach ($newValues as $langId => $text) {
            if (!isset($oldValues[$langId])) {
                $oldValues[$langId] = '';
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

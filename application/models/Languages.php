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
 * Languages model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_Languages extends Msd_Application_Model
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
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $tableConfig              = $this->_config->getParam('table');
        $this->_tableLanguages    = $tableConfig['languages'];
        $this->_tableTranslations = $tableConfig['translations'];
        $this->_tableKeys         = $tableConfig['keys'];
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
     * Saves language data to the database.
     *
     * @param int    $id            Internal id of the language
     * @param int    $active        Active state of the language
     * @param string $locale        Locale of the new language (e.g. en, de)
     * @param string $name          Name of the new language (e.g. English, Detusch)
     * @param string $flagExtension Extension of the flag file
     *
     * @return bool
     */
    public function saveLanguage($id, $active, $locale, $name, $flagExtension)
    {
        if ($id == 0 && $this->localeExists($locale)) {
            return "The specified locale '$locale' already exists.";
        }
        $locale = $this->_dbo->escape($locale);
        $name = $this->_dbo->escape($name);
        $flagExtension = $this->_dbo->escape($flagExtension);
        $sql = "INSERT INTO `{$this->_tableLanguages}` (`id`, `active`, `locale`, `name`, `flag_extension`) VALUES
            ($id, $active, '$locale', '$name', '$flagExtension') ON DUPLICATE KEY UPDATE `locale` = '$locale',
            `name` = '$name', `flag_extension` = '$flagExtension', `active` = $active";
        return $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Saves the state of a language
     *
     * @param int  $id    Internal id of the language
     * @param int $status State of the language
     *
     * @return bool
     */
    public function saveLanguageStatus($id, $status)
    {
        $id     = (int) $id;
        $status = (int) $status;
        $sql    = "UPDATE `{$this->_tableLanguages}` SET `active` = " . $status . ' WHERE `id` = ' . $id;
        return $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Loads language data from database
     *
     * @param int $id Internal id of the language
     *
     * @return array
     */
    public function getLanguageById($id)
    {
        $sql = "SELECT `id`, `active`, `locale`, `name`, `flag_extension` FROM `{$this->_tableLanguages}`
            WHERE `id` = $id";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return isset($res[0]) ? $res[0] : array();
    }

    /**
     * Retrieves all languages and their meta data.
     *
     * @param string $filter      String to filter the languages (effects lang locale and lang name)
     * @param int    $offset      Offset of entry where the result starts
     * @param int    $recsPerPage Number of records per page
     * @param bool   $activeOnly  Return only active languages
     *
     * @return array
     */
    public function getAllLanguages($filter = '', $offset = 0, $recsPerPage = 0, $activeOnly = true)
    {
        $where = '';
        $limit = '';
        if ($filter > '') {
            $filter = $this->_dbo->escape($filter);
            $where = "WHERE (`locale` LIKE '%$filter%' OR `name` LIKE '%$filter%')";
        }
        if ($recsPerPage > 0) {
            $recsPerPage = $this->_dbo->escape($recsPerPage);
            $offset = $this->_dbo->escape($offset);
            $limit = "LIMIT $offset, $recsPerPage";
        }
        if ($activeOnly) {
            if ($where != '') {
                $where .= " AND";
            } else {
                $where = "WHERE";
            }
            $where .= " `active` = 1";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS `id`, `active`, `locale`, `name`, `flag_extension`,
                (`flag_extension` != '') hasFlag
            FROM `{$this->_tableLanguages}` $where ORDER BY `locale` ASC $limit";
        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        $languages = array();
        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $languages[$row['id']] = $row;
        }
        return $languages;
    }

    /**
     * Checks for existing locale.
     *
     * @param string $locale Locale to check
     *
     * @return bool
     */
    public function localeExists($locale)
    {
        $locale = $this->_dbo->escape($locale);
        $sql = "SELECT `locale` FROM `{$this->_tableLanguages}` WHERE `locale` = '$locale' LIMIT 1";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (isset($res[0]['locale'])) {
            return true;
        }
        return false;
    }

    /**
     * Deletes the flag entry in database of the given language.
     *
     * @param int $languageId Id of the language
     *
     * @return void
     */
    public function deleteFlag($languageId)
    {
        $languageId = (int) $languageId;
        $sql = "UPDATE `{$this->_tableLanguages}` SET `flag_extension` = '' WHERE `id` = $languageId";
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Retrieves the current fallback language.
     *
     * @return int|bool
     */
    public function getFallbackLanguage()
    {
        $sql = "SELECT `id` FROM `{$this->_tableLanguages}` WHERE `is_fallback` = 1";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (empty($res)) {
            // fallback if no fallback language is set, fetch first found active language
            $sql = "SELECT `id` FROM `{$this->_tableLanguages}` WHERE `active` = 1 LIMIT 1";
            $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        }

        return (isset($res[0]['id'])) ? $res[0]['id'] : false;
    }

    /**
     * Sets the fallback language for empty entries.
     *
     * @param int $langId Id of the new fallback language.
     *
     * @return void
     */
    public function setFallbackLanguage($langId)
    {
        $langId = $this->_dbo->escape($langId);
        $sql = "UPDATE `{$this->_tableLanguages}` SET `is_fallback` = 0 WHERE `is_fallback` = 1";
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
        $sql = "UPDATE `{$this->_tableLanguages}` SET `is_fallback` = 1 WHERE `id` = $langId";
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * returns the language id of the given locale.
     *
     * @param string $locale locale
     *
     * @return int
     */
    public function getLanguageIdFromLocale($locale)
    {
        $sql = "SELECT `id` FROM `{$this->_tableLanguages}` WHERE `locale` = '$locale'";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return (isset($res[0]['id'])) ? $res[0]['id'] : 0;
    }

    /**
     * Delete a language
     *
     * @param int $languageId Id of language to delete
     *
     * @return bool
     */
    public function deleteLanguage($languageId)
    {
        $sql = "DELETE FROM `{$this->_tableLanguages}` WHERE `id` = " . intval($languageId);
        return (bool) $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
    }

    /**
     * Triggers "OPTIMIZE TABLE" for all tables to keep them defragmented and at best performance
     *
     * @return array
     */
    public function optimizeAllTables()
    {
        $tables = $this->_config->getParam('table');
        $sql = 'OPTIMIZE TABLE `' . implode('`, `', $tables) . '`';
        return $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
    }

    /**
     * Get last inserted language id
     *
     * @return bool|int
     */
    public function getLastInsertedId()
    {
        return $this->_dbo->getLastInsertId();
    }

}

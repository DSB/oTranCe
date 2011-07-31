<?php

class Application_Model_Languages
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
        $this->_dbo = Msd_Db::getAdapter();
        $this->_dbo->selectDb($this->_database);
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
     * @return bool|string
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
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
        return true;
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
    public function getAllLanguages($filter = '', $offset = 0, $recsPerPage = 0, $activeOnly = false)
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
        $sql = "SELECT SQL_CALC_FOUND_ROWS `id`, `active`, `locale`, `name`, (`flag_extension` != '') hasFlag
            FROM `{$this->_tableLanguages}` $where ORDER BY `locale` ASC $limit";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        return $res;
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
     * Deletes the flag or the given language.
     *
     * @param int $id Id of the language
     *
     * @return void
     */
    public function deleteFlag($id)
    {
        $id = $this->_dbo->escape($id);
        $sql = "UPDATE `{$this->_tableLanguages}` SET `flag_extension` = '' WHERE `id` = $id";
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Retrieves the current fallback language.
     *
     * @return int|false
     */
    public function getFallbackLanguage()
    {
        $sql = "SELECT `id` FROM `{$this->_tableLanguages}` WHERE `is_fallback` = 1";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
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
        $sql = "UPDATE `{$this->_tableLanguages}` SET `is_fallback` = 0";
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
}

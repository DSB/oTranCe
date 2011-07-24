<?php

class Application_Model_FileTemplates
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
    private $_tableFiletemplates;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_config = Msd_Configuration::getInstance();
        $this->_database = $this->_config->get('config.dbuser.db');
        $this->_tableFiletemplates = $this->_config->get('config.table.filetemplates');
        $this->_dbo = Msd_Db::getAdapter();
        $this->_dbo->selectDb($this->_database);
    }

    /**
     * Get file templates
     *
     * @param string $order       Name of the column to order the file list
     * @param string $filter      String to filter the templates (effects lang locale and lang name)
     * @param int    $offset      Offset of entry where the result starts
     * @param int    $recsPerPage Number of records per page
     *
     * @return array
     */
    public function getFileTemplates($order = '', $filter = '', $offset = 0, $recsPerPage = 0)
    {
        $where = '';
        $limit = '';
        $order = $this->_dbo->escape($order);
        if ($filter > '') {
            $filter = $this->_dbo->escape($filter);
            $where = "WHERE `name` LIKE '%$filter%' OR `filename` LIKE '%$filter%'";
        }
        if ($recsPerPage > 0) {
            $recsPerPage = $this->_dbo->escape($recsPerPage);
            $offset = $this->_dbo->escape($offset);
            $limit = "LIMIT $offset, $recsPerPage";
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$this->_database}`.`{$this->_tableFiletemplates}`";
        if ($where > '') {
            $sql .= ' ' . $where;
        }
        if ($order > '') {
            $sql .= ' ORDER BY `' . $order .'` ' . $limit;
        }
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
     * Retrieves data for the given file template.
     * If the template ID doesn't exists or ID is set to 0 (create new template), an empty (faked) record will be
     * returned.
     *
     * @param int $templateId ID of the template to retrieve.
     *
     * @return array
     */
    public function getFileTemplate($templateId)
    {
        // Fake db row for new file template or if the result set is empty.
        $emptyTemplate = array(
            'id' => 0,
            'name' => '',
            'header' => '',
            'footer' => '',
            'content' => '',
            'filename' => '',
        );
        // If a new file template is created, return empty db row immediately.
        if ($templateId == 0) {
            return $emptyTemplate;
        }
        // Escape ID. It comes from an user input or url parameter, so we can't trust them.
        $templateId = $this->_dbo->escape($templateId);

        // Build and execute the SQL statement to get file template db record.
        $sql = "SELECT * FROM `{$this->_database}`.`{$this->_tableFiletemplates}` WHERE `id` = $templateId LIMIT 1";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        // Check for empty result.
        if (isset($res[0])) {
            return $res[0];
        }
        return $emptyTemplate;
    }

    /**
     * Saves a file template to database.
     *
     * @param int    $id       Id of the template.
     * @param string $name     Name of the template.
     * @param string $header   Template for file header.
     * @param string $content  Template for language "array" content.
     * @param string $footer   Template for file footer.
     * @param string $filename Template for filename creation.
     *
     * @return bool
     */
    public function saveFileTemplate($id, $name, $header, $content, $footer, $filename)
    {
        $id        = (int) $id;
        $name      = $this->_dbo->escape($name);
        $header    = $this->_dbo->escape($header);
        $content   = $this->_dbo->escape($content);
        $footer    = $this->_dbo->escape($footer);
        $filename  = $this->_dbo->escape($filename);

        $sql = "INSERT INTO `{$this->_database}`.`{$this->_tableFiletemplates}`
            (`id`, `name`, `header`, `content`, `footer`, `filename`) VALUES
            ($id, '$name', '$header', '$content', '$footer', '$filename') ON DUPLICATE KEY UPDATE `name` = '$name',
            `header` = '$header', `content` = '$content', `footer` = '$footer', `filename` = '$filename'";
        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        return $res;
    }

    /**
     * Retrieves the file templates as an associated array.
     *
     * @return array
     */
    public function getFileTemplatesAssoc()
    {
        $sql = "SELECT * FROM `{$this->_database}`.`{$this->_tableFiletemplates}`";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        $fileTemplates = array();
        if (isset($res[0])) {
            foreach ($res as $row) {
                $fileTemplates[$row['id']] = $row;
            }
        }

        return $fileTemplates;
    }
}

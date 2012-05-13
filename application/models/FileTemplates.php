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
 * File template model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_FileTemplates extends Msd_Application_Model
{
    /**
     * Database table containing file templates
     * @var string
     */
    private $_tableFiletemplates;

    /**
     * Database table containing language var keys
     * @var string
     */
    private $_tableKeys;

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $tableConfig               = $this->_config->getParam('table');
        $this->_tableFiletemplates = $tableConfig['filetemplates'];
        $this->_tableKeys          = $tableConfig['keys'];
        $this->_tableTranslations  = $tableConfig['translations'];
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
        $templateId = (int) $templateId;
        // Fake db row for new file template or if the result set is empty.
        $template = array(
            'id' => 0,
            'name' => '',
            'header' => '',
            'footer' => '',
            'content' => '',
            'filename' => '',
        );

        // Build and execute the SQL statement to get file template db record.
        $sql = "SELECT * FROM `{$this->_database}`.`{$this->_tableFiletemplates}` WHERE `id` = $templateId LIMIT 1";
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        // Check for empty result.
        if (isset($res[0])) {
            $template = $res[0];
        }
        return $template;
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

    /**
     * Deletes a file template and assigns the dependent language keys to a new template id or deletes them.
     *
     * @param int $templateId  ID of the template to delete.
     * @param int $replacement ID of the new template to assign to the dependent language keys. (0=delete keys)
     *
     * @return array
     */
    public function deleteFileTemplate($templateId, $replacement = 0)
    {
        $replacement = (int) $replacement;
        $templateId  = (int) $templateId;
        $result = array(
            'delete' => false,
            'update' => false,
        );

        if ($replacement == 0) {
            $res = $this->_deleteFileTemplate($templateId);
        } else {
            $sql = "UPDATE `{$this->_database}`.`{$this->_tableKeys}` SET `template_id` = "
                . $replacement . " WHERE `template_id` = " . $templateId;
            $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        }
        $result['update'] = $res;

        // now delete file template
        $sql = "DELETE FROM `{$this->_database}`.`{$this->_tableFiletemplates}` WHERE `id` = " . $templateId;
        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        $result['delete'] = $res;

        return $result;
    }

    /**
     * Delete all translations and all keys assigned to the given file template
     *
     * @param int $templateId Id of template
     *
     * @return bool
     */
    private function _deleteFileTemplate($templateId)
    {
        // first get all key-Ids
        $sql = "SELECT `id` FROM `{$this->_database}`.`{$this->_tableKeys}` "
            . " WHERE `template_id` = " . $templateId;
        $res = $this->_dbo->query($sql, MSD_DB::ARRAY_ASSOC);
        if (empty($res[0])) {
            // nothing to delete
            return true;
        }

        $keyIds = array();
        foreach ($res as $data) {
            $keyIds[] = $data['id'];
        }
        // delete all translations of these keys
        $sql = "DELETE FROM `{$this->_database}`.`{$this->_tableTranslations}` "
            . " WHERE `key_id` IN (" . implode(',', $keyIds) . ')';
        $res = $this->_dbo->query($sql, MSD_DB::SIMPLE);

        // delete all keys assigned to that file template
        $sql = "DELETE FROM `{$this->_database}`.`{$this->_tableKeys}` "
            . " WHERE `template_id` = " . $templateId;
        $res &= $this->_dbo->query($sql, MSD_DB::SIMPLE);
        return $res;
    }
}

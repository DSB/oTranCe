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
 * Export-Log model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_ExportLog extends Msd_Application_Model
{
    /**
     * Name of table containing export log data
     * @var string
     */
    private $_tableExportLog;

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $tableConfig           = $this->_config->getParam('table');
        $this->_tableExportLog = $tableConfig['exportlog'];
    }

    /**
     * Retrieves all files of an export process.
     *
     * @param string $exportId Id of the export process.
     *
     * @return array
     */
    public function getFileList($exportId)
    {
        $sql   = 'SELECT `filename` FROM `' . $this->_tableExportLog . '` WHERE'
            . ' `export_id` = \'' . $this->_dbo->escape($exportId) . "'"
            . ' ORDER BY `filename` ASC';
        $res   = $this->_dbo->query($sql, Msd_Db::SIMPLE);
        $files = array();
        while ($row = $res->fetch_assoc()) {
            $files[] = $row['filename'];
        }
        return $files;
    }

    /**
     * Add a file entry to export log.
     *
     * @param string $exportId Id of the export process.
     * @param string $filename Filename to add.
     *
     * @return void
     */
    public function add($exportId, $filename)
    {
        $sql = 'INSERT INTO `' . $this->_tableExportLog . '` (`export_id`, `filename`) VALUES (\''
            . $this->_dbo->escape($exportId) . "', '" . $this->_dbo->escape($filename) . "')";
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Delete all entries from an export process.
     *
     * @param string $exportId Id of the export process.
     *
     * @return void
     */
    public function delete($exportId)
    {
        $this->_dbo->query(
            'DELETE FROM `' . $this->_tableExportLog . '` WHERE ' .
            '`export_id` = \'' . $this->_dbo->escape($exportId) . '\'',
            Msd_Db::SIMPLE
        );
    }

    /**
     * Retrives the number of exports.
     *
     * @return int
     */
    public function getExportsCount()
    {
        $result = $this->_dbo->query('SELECT COUNT(*) FROM `' . $this->_tableExportLog . '`', Msd_Db::SIMPLE);
        $row    = $result->fetch_row();
        return (int)$row[0];
    }
}

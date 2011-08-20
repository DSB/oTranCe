<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan
 * Date: 07.08.11
 * Time: 13:43
 * To change this template use File | Settings | File Templates.
 */

class Application_Model_ExportLog extends Msd_Application_Model
{
    /**
     * Retrieves all files of an export process.
     *
     * @param string $exportId Id of the export process.
     *
     * @return array
     */
    public function getFileList($exportId)
    {
        $sql = "SELECT `filename` FROM `exportlog` WHERE `export_id` = '" . $this->_dbo->escape($exportId) . "'";
        $res = $this->_dbo->query($sql, Msd_Db::SIMPLE);
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
        $sql = "INSERT INTO `exportlog` (`export_id`, `filename`) VALUES ('" . $this->_dbo->escape($exportId) . "', '"
            . $this->_dbo->escape($filename) . "')";
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
            "DELETE FROM `exportlog` WHERE `export_id` = '" . $this->_dbo->escape($exportId) . "'",
            Msd_Db::SIMPLE
        );
    }
}

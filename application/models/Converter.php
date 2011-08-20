<?php
/**
 * Helper to class which uses MySQL to convert strings to UTF-8
 */
class Application_Model_Converter extends Msd_Application_Model
{
    /**
     * Database table used for converting
     * @var string
     */
    private $_tableConversions;

    public function init()
    {
        $dbTables = $this->_config->getParam('table');
        $this->_tableConversions = $dbTables['conversions'];
    }

    /**
     * Convert data to UTF-8 using MySQL
     *
     * @param string $inputCharset The current character set of the string
     * @param string $text
     *
     * @return false|string UTF-8 encoded string
     */
    public function convertData($inputCharset, $text)
    {
        $this->_dbo->selectDb($this->_database);
        $this->_dbo->setConnectionCharset($inputCharset);
        $id = $this->_dbo->escape(Zend_Session::getId());
        $text = $this->_dbo->escape($text);

        $stmt = $this->_dbo->prepare('INSERT INTO `' . $this->_database . '`.`' . $this->_tableConversions. '` '
                . ' (`id`, `text`) VALUES (? , ?)');
        $stmt->bind_param('ss', $id, $text);
        $stmt->execute();
        $stmt->close();

        // re-read the value in utf-8
        $this->_dbo->setConnectionCharset('utf8');
        $sql = 'SELECT `text` FROM `' . $this->_database . '`.`' . $this->_tableConversions. '` '
                .' WHERE `id`=\'' . $id . '\'';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (!isset($res[0]['text'])) {
            return false;
        }
        $data = $res[0]['text'];
        $data = stripcslashes($data);
        // now delete entry from db, we don't need it anymore
        $sql = 'DELETE FROM `' . $this->_database . '`.`' . $this->_tableConversions. '` '
                .' WHERE `id`=\'' . $id . '\'';
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
        return $data;
    }

}

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
 * Converter model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_Converter extends Msd_Application_Model
{
    /**
     * Database table used for converting
     * @var string
     */
    private $_tableConversions;

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $dbTables = $this->_config->getParam('table');
        $this->_tableConversions = $dbTables['conversions'];
    }

    /**
     * Convert data to UTF-8 using MySQL
     *
     * @param string $inputCharset The current character set of the string
     * @param string $text         The text to convert
     *
     * @return bool|string UTF-8 encoded string
     */
    public function convertData($inputCharset, $text)
    {
        if ($inputCharset === 'utf8') {
            //nothing to convert - return original text immediately without bothering mysql
            return $text;
        }
        $data = $text;
        $this->_dbo->selectDb($this->_database);
        $this->_dbo->setConnectionCharset($inputCharset);
        $id   = $this->_dbo->escape(Zend_Session::getId());
        $text = $this->_dbo->escape($text);
        $sql  = 'INSERT INTO `' . $this->_database . '`.`' . $this->_tableConversions. '` '
                . ' (`id`, `text`) VALUES (? , ?)';
        $stmt = $this->_dbo->prepare($sql);
        $stmt->bind_param('ss', $id, $text);
        $stmt->execute();
        if ((int) $stmt->errno !== 0) {
            $stmt->close();
            return false;
        }
        $stmt->close();

        // re-read the value in utf-8
        $this->_dbo->setConnectionCharset('utf8');
        $sql = 'SELECT `text` FROM `' . $this->_database . '`.`' . $this->_tableConversions. '` '
                .' WHERE `id`=\'' . $id . '\'';
        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);
        if (isset($res[0]['text'])) {
            $data = $res[0]['text'];
            $data = stripcslashes($data);
            // now delete entry from db, we don't need it anymore
            $sql = 'DELETE FROM `' . $this->_database . '`.`' . $this->_tableConversions. '` '
                    .' WHERE `id`=\'' . $id . '\'';
            $this->_dbo->query($sql, Msd_Db::SIMPLE);
        }
        return $data;
    }

}

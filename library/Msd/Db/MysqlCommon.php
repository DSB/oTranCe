<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Db
 * @version         SVN: $rev: 1205 $
 * @author          $Author$
 */

/**
 * Class offers some db related methods that are equal for Mysql and MySQLi.
 *
 * @package         MySQLDumper
 * @subpackage      Db
 */
abstract class Msd_Db_MysqlCommon extends Msd_Db
{
    /**
     * Get the list of tables of given database
     *
     * @param string $dbName Name of database
     *
     * @return array
     */
    public function getTables($dbName)
    {
        $tables = array();
        $sql = 'SHOW TABLES FROM `' . $dbName . '`';
        $res = $this->query($sql, self::ARRAY_NUMERIC);
        foreach ($res as $val) {
           $tables[] = $val[0];
        }
        return $tables;
    }

    /**
     * Get information of databases
     *
     * Gets list and info of all databases that the actual MySQL-User can access
     * and saves it in $this->databases.
     *
     * @return array
     */
    public function getDatabases()
    {
        $query = 'SELECT * FROM `information_schema`.`SCHEMATA` '
                . 'ORDER BY `SCHEMA_NAME` ASC';
        $res = $this->query($query, self::ARRAY_ASSOC, true);
        foreach ($res as $row) {
            $database = $row['SCHEMA_NAME'];
            unset($row['SCHEMA_NAME']);
            $this->_databases[$database] = $row;
        }
        return $this->_databases;
    }

    /**
     * Return assoc array with the names of accessable databases
     *
     * @return array Assoc array with database names
     */
    public function getDatabaseNames()
    {
        if ($this->_databases == null) {
            $this->getDatabases();
        }
        return array_keys($this->_databases);
    }
    /**
     * Returns the actual selected database.
     *
     * @return string
     */
    public function getSelectedDb()
    {
        return $this->_dbSelected;
    }

    /**
     * Returns the CREATE Statement of a table.
     *
     * @param string $table
     *
     * @return string
     */
    public function getTableCreate($table)
    {
        $sql = 'SHOW CREATE TABLE `' . $table . '`';
        $res = $this->query($sql, self::ARRAY_ASSOC);
        return $res[0]['Create Table'];
    }

    /**
     * Gets the full description of all columns of a table.
     *
     * Saves it to $this->metaTables[$database][$table].
     *
     * @param string $table
     *
     * @return array
     */
    public function getTableColumns($table)
    {
        $dbName  = $this->getSelectedDb();
        $sql = 'SHOW FULL FIELDS FROM `' . $table . '`';
        $res = $this->query($sql, self::ARRAY_ASSOC);
        if (!isset($this->_metaTables[$dbName])) {
            $this->_metaTables[$dbName] = array();
        }
        if (is_array($res)) {
            $this->_metaTables[$dbName][$table] = array();
            foreach ($res as $r) {
                $this->_metaTables[$dbName][$table][$r['Field']] = $r;
            }
        }
        return $this->_metaTables[$dbName][$table];
    }

    /**
     * Optimize given table.
     *
     * Returns false on error or Sql's Msg_text if query succeeds.
     *
     * @param string $table Name of table
     *
     * @return string|boolean
     */
    function optimizeTable($table)
    {
        $sql = 'OPTIMIZE TABLE `' . $table . '`';
        $res = $this->query($sql, Msd_Db::ARRAY_ASSOC);
        if (isset($res[0]['Msg_text'])) {
            return $res[0];
        } else {
            return false;
        }
    }

    /**
     * Get list of known charsets from MySQL-Server.
     *
     * @return array
     */
    public function getCharsets()
    {
        if (!empty($this->_charsets)) {
            return $this->_charsets;
        }
        $result = $this->query('SHOW CHARACTER SET', self::ARRAY_ASSOC);
        $this->_charsets = array();
        foreach ($result as $r) {
            $this->_charsets[$r['Charset']] = $r;
        }
        @ksort($this->_charsets);
        return $this->_charsets;
    }

    /**
     * Gets extended table information for one or all tables.
     *
     * @param string $tableName
     * @param string $databaseName
     *
     * @return array
     */
    public function getTableStatus($tableName = null, $databaseName = null)
    {
        if ($databaseName === null) {
            $databaseName = $this->getSelectedDb();
        }
        $sql = 'SELECT * FROM `information_schema`.`TABLES` WHERE '
                . '`TABLE_SCHEMA`=\''.$databaseName.'\'';
        if ($tableName !== null) {
            $sql .= ' AND `TABLE_NAME` LIKE \'' . $tableName . '\'';
        }
        $res = $this->query($sql, self::ARRAY_ASSOC);
        return $res;
    }

    /**
     * Get variables of SQL-Server and return them as assoc array
     *
     * @return array
     */
    public function getVariables()
    {
        $ret = array();
        $variables = $this->query('SHOW VARIABLES', Msd_Db::ARRAY_ASSOC);
        foreach ($variables as $val) {
            $ret[$val['Variable_name']] = $val['Value'];
        }
        return $ret;
    }

    /**
     * Get global status variables of SQL-Server and return them as assoc array
     *
     * @return array
     */
    public function getGlobalStatus()
    {
        $ret = array();
        $variables = $this->query('SHOW GLOBAL STATUS', Msd_Db::ARRAY_ASSOC);
        foreach ($variables as $val) {
            $ret[$val['Variable_name']] = $val['Value'];
        }
        return $ret;
    }

    /**
     * Get the number of records of a table by query SELECT COUNT(*) and output
     * it as return of ajax request.
     *
     * @param string $tableName The name of the table
     * @param string $dbName    The name of the database
     *
     * @return integer The number of rows isnide table
     */
    public function getNrOfRowsBySelectCount($tableName, $dbName = null)
    {
        if ($dbName === null) {
            $dbName = $this->getSelectedDb();
        };
        $sql = 'SELECT COUNT(*) as `Rows` FROM `%s`.`%s`';
        $sql = sprintf($sql, $dbName, $tableName);
        $rows = $this->query($sql, Msd_Db::ARRAY_ASSOC);
        return (int) $rows[0]['Rows'];
    }

    /**
     * Return the last inserted id set on auto_increment for last insert action
     *
     * @return false|int
     */
    public function getLastInsertId()
    {
        $sql = 'SELECT LAST_INSERT_ID() as `id`';
        $res = $this->query($sql, Msd_Db::ARRAY_ASSOC);
        return isset($res[0]['id']) ? $res[0]['id'] : false;
    }
}
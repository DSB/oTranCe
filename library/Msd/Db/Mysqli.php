<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Db
 * @version         SVN: $rev: 1208 $
 * @author          $Author$
 */
/**
 * Capsules all database related actions.
 *
 * @package         MySQLDumper
 * @subpackage      Db
 */
class Msd_Db_Mysqli extends Msd_Db_MysqlCommon
{
    /**
     * Mysqli instance
     * @var mysqli
     */
    private $_mysqli = null;

    /**
     * Result handle
     * @var mysqli_result
     */
    private $_resultHandle = null;

    /**
     * Establish a connection to MySQL.
     *
     * Create a connection to MySQL and store the connection handle in
     * $this->connectionHandle.
     *
     * @throws Msd_Exception
     *
     * @return boolean
     **/
    protected function _dbConnect()
    {
        $errorReporting = error_reporting(0);
        if ($this->_port == 0) {
            $this->_port = 3306;
        }

        $this->_mysqli = new mysqli(
            $this->_server,
            $this->_user,
            $this->_password,
            $this->_dbSelected,
            $this->_port,
            $this->_socket
        );
        error_reporting($errorReporting);
        if ($this->_mysqli->connect_errno) {
            $error = $this->_mysqli->connect_error;
            $errno = $this->_mysqli->connect_errno;
            $this->_mysqli = null;
            throw new Msd_Exception($error, $errno);
        }
        $this->setConnectionCharset();
        return true;
    }

    public function __destruct()
    {
        if ($this->_mysqli instanceof mysqli) {
            $this->_mysqli->close();
        }
    }

    /**
     * Returns the connection handle if already set or creates one.
     *
     * @return mysqli The instance of mysqli
     */
    private function _getHandle()
    {
        if (!$this->_mysqli instanceof mysqli) {
            $this->_dbConnect();
        }
        return $this->_mysqli;
    }

    /**
     * Returns the version nr of MySql server.
     *
     * @return string Version nr
     */
    public function getServerInfo()
    {
        return $this->_getHandle()->server_info;
    }

    /**
     * Return version nr of MySql client.
     *
     * @return string Version nr
     */
    public function getClientInfo()
    {
        return $this->_getHandle()->client_info;
    }

    /**
     * Sets the character set of the MySQL-connection.
     *
     * Trys to set the connection charset and returns it.
     *
     * @param string $charset The wanted charset of the connection
     *
     * @return string The set charset
     */
    public function setConnectionCharset($charset = 'utf8')
    {
        if (!@$this->_getHandle()->set_charset($charset)) {
            $this->sqlError(
                $charset . ' ' . $this->_mysqli->error,
                $this->_mysqli->errno
            );
        }
        $this->_connectionCharset = $this->_getHandle()->character_set_name();
        return $this->_connectionCharset;
    }

    /**
     * Select the given database to use it as the target for following queries.
     *
     * Returns true if selection was succesfull, otherwise false.
     *
     * @param string  $database Database to select
     *
     * @return bool True on success
     */
    public function selectDb($database)
    {
        $res = @$this->_getHandle()->select_db($database);
        if ($res === false) {
            return $this->_getHandle()->error;
        } else {
            $this->_dbSelected = $database;
            return true;
        }
    }

    /**
     * Execute a query and set _resultHandle
     *
     * If $getRows is true all rows are fetched and returned.
     * If $getRows is false, query will be executed, but the result handle
     * is returned.
     *
     * @param string  $query   The query to execute
     * @param int     $kind    Type of result set
     * @param bool $getRows Wether to fetch all rows and return them
     *
     * @return mysqli_result|array|bool
     */
    public function query($query, $kind = self::ARRAY_OBJECT, $getRows = true)
    {
        $this->_resultHandle = $this->_getHandle()->query($query);
        if (false === $this->_resultHandle) {
            $this->sqlError(
                $this->_getHandle()->error,
                $this->_getHandle()->errno
            );
            return false;
        }
        if (!$this->_resultHandle instanceof mysqli_result || $kind === self::SIMPLE) {
            return $this->_resultHandle;
        }
        // return result set?
        if ($getRows) {
            $ret = array();
            while ($row = $this->getNextRow($kind)) {
                $ret[] = $row;
            }
            $this->_resultHandle = null;
            return $ret;
        }

        return null;
    }

    /**
     * Get next row from a result set that is returned by $this->query().
     *
     * Can be used to walk through result sets.
     *
     * @param int $kind
     *
     * @return array|object
     */
    public function getNextRow($kind)
    {
        switch ($kind)
        {
            case self::ARRAY_ASSOC:
                return $this->_resultHandle->fetch_assoc();
            case self::ARRAY_OBJECT:
                return $this->_resultHandle->fetch_object();
                break;
            case self::ARRAY_NUMERIC:
                return $this->_resultHandle->fetch_array(MYSQLI_NUM);
                break;
            default:
                return $this->_resultHandle->fetch_array(); // unknown fetch method; return MYSQLI_BOTH.
        }
    }

    /**
     * Wrapper for mysqli's preare method
     *
     * @param string $prepare The query to prepare
     *
     * @return mysqli_stmt
     */
    public function prepare($prepare)
    {
        return $this->_mysqli->prepare($prepare);
    }

    /**
     * Gets the number of affected rows for the last executed query.
     *
     * @see inc/classes/db/MsdDbFactory#affectedRows()
     * @return integer
     */
    public function getAffectedRows()
    {
        return $this->_getHandle()->affected_rows;
    }

    /**
     * Escape a value with real_escape_string() to use it in a query.
     *
     * @see inc/classes/db/MsdDbFactory#escape($val)
     * @param mixed $val The value to escape
     *
     * @return mixed
     */
    public function escape($val)
    {
        return $this->_getHandle()->real_escape_string($val);
    }

    /**
     * Get nr of rows of last query (query needs to invoked using SQL_CALC_FOUND_ROWS)
     *
     * @return integer
     */
    public function getRowCount()
    {
        $res = $this->query('SELECT FOUND_ROWS() AS `results`', Msd_Db::ARRAY_ASSOC);
        if (!isset($res[0]['results'])) {
            return 0;
        }
        return (int) $res[0]['results'];
    }

}

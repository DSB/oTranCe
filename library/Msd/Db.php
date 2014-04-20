<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Db
 * @version         SVN: $rev: 1212 $
 * @author          $Author$
 */

/**
 * DB-Factory
 *
 * @abstract
 * @package         MySQLDumper
 * @subpackage      Db
 */
abstract class Msd_Db
{
    // define result set types
    const ARRAY_NUMERIC = 0; // return resultset as numeric array
    const ARRAY_ASSOC = 1; // return resultset as associative array
    const ARRAY_OBJECT = 2; // return resultset as array of object
    const SIMPLE = 3; // return result as boolean
    /**
     * SQL-Server
     *
     * @var string
     */
    protected $_server;

    /**
     * SQL user used to authenticate at server
     *
     * @var string
     */
    protected $_user;

    /**
     * SQL user password used to authenticate
     *
     * @var string
     */
    protected $_password;

    /**
     * Port used for connection to server
     *
     * @var int
     */
    protected $_port;

    /**
     * Socket used for connection
     *
     * @var string
     */
    protected $_socket;

    /**
     * List of databases and default settings
     *
     * @var array
     */
    protected $_databases = null;

    /**
     * List of views
     *
     * @var array
     */
    protected $_views = null;

    /**
     * charset to use (default utf8)
     *
     * @var string
     */
    protected $_connectionCharset = 'utf8';

    /**
     * the selected db
     *
     * @var string
     */
    protected $_dbSelected = '';

    /**
     * List of cached tables
     *
     * @var array
     */
    protected $_tables = array();

    /**
     * Meta informations about tables
     *
     * @var array
     */
    protected $_metaTables = array();

    /**
     * Charsets the server supports (cached)
     *
     * @var array
     */
    protected $_charsets = array();

    public static $dbObject;

    /**
     * Init database object
     *
     * @param array $options Array containing connection options
     *
     * @return Msd_Db
     */
    protected function __construct($options)
    {
        $this->_server   = $options['host'];
        $this->_user     = $options['user'];
        $this->_password = $options['pass'];
        $this->_port     = (int)$options['port'];
        $this->_socket   = $options['socket'];
    }

    /**
     * Create database adapter
     *
     * @param array $options Connection options
     *
     * @return Msd_Db_MysqlCommon
     */
    public static function getAdapter($options = null)
    {
        if (self::$dbObject !== null) {
            return self::$dbObject;
        }
        if ($options === null) {
            $config       = Msd_Registry::getConfig();
            $dbUserConfig = $config->getParam('dbuser');
            $options      = array(
                'host'   => $dbUserConfig['host'],
                'user'   => $dbUserConfig['user'],
                'pass'   => $dbUserConfig['pass'],
                'port'   => (int)$dbUserConfig['port'],
                'socket' => $dbUserConfig['socket'],
            );
        }
        self::$dbObject = new Msd_Db_Mysqli($options);

        return self::$dbObject;
    }

    /**
     * Establish a connection to SQL-Server. The connection is stored and used
     * for further DB requests.
     *
     * @return bool if connection is successfull
     * */
    abstract protected function _dbConnect();

    /**
     * Get selected database
     *
     * @return string
     */
    abstract public function getSelectedDb();

    /**
     * Get version nr of sql server
     *
     * @return string
     */
    abstract public function getServerInfo();

    /**
     * Get version nr of sql client
     *
     * @return string
     */
    abstract public function getClientInfo();

    /**
     * Get all known character sets of this SQL-Server.
     *
     * @return array
     */
    abstract public function getCharsets();

    /**
     * Set character set of the MySQL-connection.
     *
     * Trys to set  the connection charset and returns it.
     * Throw Exception on failure.
     *
     * @param string $charset
     *
     * @throws Exception
     *
     * @return string
     */
    abstract public function setConnectionCharset(
        $charset = 'utf8'
    );

    /**
     * Get list of databases
     *
     * Gets list of all databases that the actual SQL-Server-User has access to
     * and saves it in $this->databases.
     * Returns true on success or false on error.
     *
     * @return array
     */
    abstract public function getDatabases();

    /**
     * Select the given database to use it as the target for following queries.
     *
     * Returns true if selection was succesfull otherwise false.
     *
     * @throws Exception
     *
     * @param string $database
     *
     * @return bool
     */
    abstract public function selectDb($database);

    /**
     * Execute a query and set _resultHandle
     *
     * If $getRows is true alls rows are fetched and returned
     *
     * @param string $query   The query to execute
     * @param int    $kind    Type of result set
     * @param bool   $getRows Whether to fetch all rows and return them
     *
     * @return boolean|array|mysqli_result
     */
    abstract public function query($query, $kind = self::ARRAY_OBJECT, $getRows = true);

    /**
     * Get next row from result set
     *
     * @param const $kind
     *
     * @return array|object
     */
    abstract function getNextRow($kind);

    /**
     * Get the list of tables of given database
     *
     * @param string $dbName Name of database
     *
     * @return array
     */
    abstract public function getTables($dbName);

    /**
     * Get the list of views of given database
     *
     * @param string $dbName Name of database
     *
     * @return array
     */
    abstract public function getViews($dbName);

    /**
     * Gets extended table information for one or all tables
     *
     * @param string|bool $table
     *
     * @return array
     */
    abstract public function getTableStatus($table = false);

    /**
     * Returns the CREATE Statement of a table.
     *
     * @throws Exception
     *
     * @param string $table Get CREATE-Statement for this table
     *
     * @return string Create statement
     */
    abstract public function getTableCreate($table);

    /**
     * Returns the CREATE Statement of a view.
     *
     * @throws Exception
     *
     * @param string $view Get CREATE-Statement for this view
     *
     * @return string Create statement
     */
    abstract public function getViewCreate($view);

    /**
     * Gets the full description of all columns of a table
     *
     * Saves list to $this->metaTables[$database][$table].
     *
     * @param string $table Table to read meta info from
     *
     * @return array
     */
    abstract public function getTableColumns($table);

    /**
     * Gets the number of affected rows of the last query
     *
     * @return int
     */
    abstract function getAffectedRows();

    /**
     * Gets the servers variables
     *
     * @return array
     */
    abstract function getVariables();

    /**
     * Escape a value for inserting it in query
     *
     * @param string $val
     *
     * @return string
     */
    abstract function escape($val);

    /**
     * Optimize a table. Returns true on success or MySQL-Error.
     *
     * @param $table string Name of table
     *
     * @return string|bool Returned optimize message or false on error
     */
    abstract function optimizeTable($table);

    /**
     * Handles a SQL-Error
     *
     * @param string $errmsg The error message
     * @param int    $errno  The error number
     *
     * @throws Msd_Exception
     *
     * @return void
     */
    public function sqlError($errmsg, $errno)
    {
        throw new Msd_Exception($errmsg, $errno);
    }
}

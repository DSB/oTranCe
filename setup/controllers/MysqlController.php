<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Archive
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Controller for setting up the MySQL connection.
 *
 * @package         MySQLDumper
 * @subpackage      Archive
 */
class MysqlController extends Setup_Controller_Abstract
{
    /**
     * Checks and stores the MySQL connection settings.
     *
     * @return void
     */
    public function checkAction()
    {
        error_reporting(0);
        $mysql = $this->_request->getParam('mysql');

        $mysqli = new mysqli(
            $mysql['host'],
            $mysql['user'],
            $mysql['pass'],
            $mysql['db'],
            $mysql['port'],
            $mysql['socket']
        );
        $success = ($mysqli->connect_error === null);
        if ($success) {
            $_SESSION['mysql'] = $mysql;
        }
        $this->_response->setBodyJson(
            array(
                'connect' => $success,
                'message' => $mysqli->connect_error,
                'number'  => $mysqli->connect_errno,
            )
        );
    }

    /**
     * Creates the database tables.
     *
     * @return void
     */
    public function createTablesAction()
    {
        $mysql = $_SESSION['mysql'];
        $mysqli = new mysqli(
            $mysql['host'],
            $mysql['user'],
            $mysql['pass'],
            $mysql['db'],
            $mysql['port'],
            $mysql['socket']
        );

        $mysqli->query('SET NAMES utf8');

        $setupSqlFile = file_get_contents($this->_config['extractDir'] . '/docs/setup.sql');
        $sqlQueries = explode(";\n", $setupSqlFile);
        $result = array(
            'success' => true,
        );

        foreach ($sqlQueries as $lineNo => $sqlQuery) {
            if (empty($sqlQuery)) {
                continue;
            }
            $result['success'] = $result['success'] && $mysqli->query($sqlQuery);
            if (!$result['success']) {
                $result['message'] = $mysqli->error;
                $result['number'] = $mysqli->errno;
                $result['line'] = $lineNo + 1;
                $result['query'] = $sqlQuery;
                break;
            }
        }

        $this->_response->setBodyJson($result);
    }
}

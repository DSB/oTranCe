<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://www.otrance.org
 *
 * @package         oTranCe
 * @subpackage      Setup_Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Controller for setting up the MySQL connection.
 *
 * @package         oTranCe
 * @subpackage      Setup_Controllers
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
            "",
            $mysql['port'],
            $mysql['socket']
        );
        $connectError   = 'connect_error';
        $connectErrorNr = 'connect_errno';
        $success        = ($mysqli->$connectError === null);
        if (!$success) {
            $this->_response->setBodyJson(
                array(
                    'connect' => $success,
                    'message' => $mysqli->$connectError,
                    'number'  => $mysqli->$connectErrorNr,
                )
            );
            return;
        }

        $_SESSION['mysql'] = $mysql;

        $stmt = $mysqli->stmt_init();
        $stmt->prepare('SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?');
        $stmt->bind_param("s", $mysql['db']);
        $stmt->execute();
        $stmt->bind_result($row);
        $stmt->fetch();
        $stmt->free_result();

        $queries = array();

        foreach ($_SESSION['setupInfo']['sql-queries'] as $queryId => $queryInfo) {
            $queries[] = array(
                'id'    => $queryId,
                'title' => $queryInfo['title'],
            );
        }

        if ($row > 0) {
            $this->_response->setBodyJson(
                array(
                    'connect' => true,
                    'dbExists' => true,
                    'message' => 'The database ' . $mysql['db']
                        . ' already exists.<br/>If you continue, all tables with the given prefix will be overwritten.',
                    'queries' => $queries,
                )
            );

            return;
        }

        $success = $mysqli->query(
            "CREATE DATABASE " . $mysql['db'] . " DEFAULT CHARSET utf8 COLLATE utf8_general_ci"
        );

        $this->_response->setBodyJson(
            array(
                'connect' => true,
                'dbExists' => false,
                'dbCreate' => $success,
                'message' => $mysqli->error,
                'queries' => $queries,
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
        $tableSearch = $mysql['prefix'] . '%';

        $stmt = $mysqli->stmt_init();
        $stmt->prepare('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME LIKE ?');
        $stmt->bind_param("ss", $mysql['db'], $tableSearch);
        $stmt->execute();
        $stmt->bind_result($row);

        $tables = array();
        while ($stmt->fetch()) {
            $tables[] = $row;
        }
        $stmt->free_result();

        $mysqli->query('DROP TABLE `' . implode('`, `', $tables) . '`');

        $sqlQueries = $_SESSION['setupInfo']['sql-queries'];
        $result = array(
            'success' => true,
        );

        $queryResults = array();

        foreach ($sqlQueries as $queryId => $queryInfo) {
            $realQuery = str_replace('{PREFIX}', $mysql['prefix'], $queryInfo['query']);
            $result['queries'][] = $realQuery;
            $queryResult = $mysqli->query($realQuery);
            $result['success'] = $result['success'] && $queryResult;
            $queryResults[$queryId] = array(
                'success' => $queryResult,
                'message' => $mysqli->error,
            );
        }

        $result['queryResults'] = $queryResults;

        $this->_response->setBodyJson($result);
    }
}

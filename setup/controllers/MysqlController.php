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
        $success = ($mysqli->connect_error === null);
        if (!$success) {
            $this->_response->setBodyJson(
                array(
                    'connect' => $success,
                    'message' => $mysqli->connect_error,
                    'number'  => $mysqli->connect_errno,
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

        if ($row > 0) {
            $this->_response->setBodyJson(
                array(
                    'connect' => true,
                    'dbExists' => true,
                    'message' => 'The database ' . $mysql['db']
                        . ' already exists.<br/>If you continue, all data in the selected database is lost.',
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

        $stmt = $mysqli->stmt_init();
        $stmt->prepare('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?');
        $stmt->bind_param("s", $mysql['db']);
        $stmt->execute();
        $stmt->bind_result($row);

        $tables = array();
        while ($stmt->fetch()) {
            $tables[] = $row;
        }
        $stmt->free_result();

        $mysqli->query('DROP TABLE `' . implode('`, `', $tables) . '`');

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

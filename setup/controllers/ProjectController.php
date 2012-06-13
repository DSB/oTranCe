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
 * Controller to manage project settings.
 *
 * @package         oTranCe
 * @subpackage      Setup_Controllers
 */
class ProjectController extends Setup_Controller_Abstract
{
    /**
     * Generates a new key for the cookie encryption.
     *
     * @param int $length Length of the key.
     *
     * @return string
     */
    protected function _generateEncryptionKey($length = 32)
    {
        $pool = array_merge(
            range(0, 9),
            range('a', 'z'),
            range('A', 'Z')
        );
        $poolSize = count($pool);

        mt_srand();
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, $poolSize - 1)];
        }
        return $key;
    }

    /**
     * Controller action for saving the whole configuration and creating the administrator user.
     *
     * @return void
     */
    public function saveAction()
    {
        $projectInfo = $this->_request->getParam('project');
        $adminInfo = $this->_request->getParam('admin');
        $mysql = $_SESSION['mysql'];
        $tablePrefix = $mysql['prefix'];

        $configIni = parse_ini_file($this->_config['extractDir'] . '/application/configs/config.dist.ini', true);
        $configIni['dbuser'] = $mysql;
        $configIni['project']['name'] = $projectInfo['name'];
        $configIni['project']['url'] = $projectInfo['url'];
        $configIni['project']['encryptionKey'] = $this->_generateEncryptionKey();

        foreach ($_SESSION['setupInfo']['sql-queries'] as $queryInfo) {
            if (!isset($queryInfo['tableName'])) {
                continue;
            }

            $configIni['table'][$queryInfo['tableName']] = $tablePrefix . $queryInfo['tableName'];
        }

        $mysqli = new mysqli(
            $mysql['host'],
            $mysql['user'],
            $mysql['pass'],
            $mysql['db'],
            $mysql['port'],
            $mysql['socket']
        );

        $createAdmin = true;
        $stmt = $mysqli->stmt_init();
        $stmt->prepare("INSERT INTO `{$tablePrefix}users` (`username`, `password`, `active`) VALUES (?, MD5(?), 1)");
        $stmt->bind_param("ss", $adminInfo['login'], $adminInfo['pass']);
        $createAdmin = $createAdmin && $stmt->execute();

        $stmt->close();


        $userId = $mysqli->insert_id;
        $stmt = $mysqli->stmt_init();
        $stmt->prepare("INSERT INTO `{$tablePrefix}userrights` (`user_id`, `right`, `value`) VALUES (?, ?, ?)");

        foreach ($_SESSION['setupInfo']['adminRights'] as $right => $value) {
            $createAdmin = $createAdmin && $stmt->bind_param("iss", $userId, $right, $value);
            $stmt->execute();
            $stmt->free_result();
        }

        include_once $this->_config['extractDir'] . '/library/Msd/Exception.php';
        include_once $this->_config['extractDir'] . '/library/Msd/Ini.php';

        $saveConfig = true;
        $ini = new Msd_Ini();
        $ini->setIniData($configIni);
        $ini->disableEscaping();
        $saveConfig = $saveConfig && $ini->saveFile($this->_config['extractDir'] . '/application/configs/config.ini');

        $saveConfig = $saveConfig && copy(
            $this->_config['extractDir'] . '/public/.htaccess.dist',
            $this->_config['extractDir'] . '/public/.htaccess'
        );

        $saveConfig = $saveConfig && (file_put_contents(APPLICATION_PATH . '/.htaccess', 'Deny From All') !== false);

        $this->_response->setBodyJson(
            array(
                'createAdmin' => $createAdmin,
                'saveConfig' => $saveConfig,
            )
        );
    }
}

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

        $configIni = parse_ini_file($this->_config['extractDir'] . '/application/configs/config.dist.ini', true);
        $configIni['dbuser'] = $mysql;
        $configIni['project']['name'] = $projectInfo['name'];
        $configIni['project']['url'] = $projectInfo['url'];
        $configIni['project']['encryptionKey'] = $this->_generateEncryptionKey();

        $mysqli = new mysqli(
            $mysql['host'],
            $mysql['user'],
            $mysql['pass'],
            $mysql['db'],
            $mysql['port'],
            $mysql['socket']
        );

        $createAdmin = true;
        $stmt = $mysqli->prepare('INSERT INTO `users` (`username`, `password`, `active`) VALUES (?, MD5(?), 1)');
        $stmt->bind_param("ss", $adminInfo['login'], $adminInfo['pass']);
        $createAdmin = $createAdmin && $stmt->execute();

        $stmt->close();

        $userId = $mysqli->insert_id;
        $rightsSql = "INSERT INTO `userrights` (`user_id`, `right`, `value`) VALUES
            ($userId, 'addLanguage', 1),
            ($userId, 'addTemplate', 1),
            ($userId, 'addUser', 1),
            ($userId, 'addVar', 1),
            ($userId, 'admin', 1),
            ($userId, 'admTabProject', 1),
            ($userId, 'createFile', 1),
            ($userId, 'deleteLanguage', 1),
            ($userId, 'deleteUsers', 1),
            ($userId, 'editConfig', 1),
            ($userId, 'editKey', 1),
            ($userId, 'editLanguage', 1),
            ($userId, 'editProject', 1),
            ($userId, 'editTemplate', 1),
            ($userId, 'editUsers', 1),
            ($userId, 'editVcs', 1),
            ($userId, 'export', 1),
            ($userId, 'importEqualVar', 1),
            ($userId, 'showBrowseFiles', 1),
            ($userId, 'showDownloads', 1),
            ($userId, 'showEntries', 1),
            ($userId, 'showExport', 1),
            ($userId, 'showImport', 1),
            ($userId, 'showLog', 1),
            ($userId, 'showStatistics', 1)";
        $createAdmin = $createAdmin && $mysqli->query($rightsSql);

        include_once $this->_config['extractDir'] . '/library/Msd/Exception.php';
        include_once $this->_config['extractDir'] . '/library/Msd/Ini.php';

        $saveConfig = true;
        $ini = new Msd_Ini();
        $ini->setIniData($configIni);
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

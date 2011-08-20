<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Auth_adapter
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Adapter to use Zend_Auth with INI files.
 *
 * @package         MySQLDumper
 * @subpackage      Auth_adapter
 */
class Msd_Auth_Adapter_Db implements Zend_Auth_Adapter_Interface
{
    /**
     * Username for authentication.
     *
     * @var string
     */
    private $_username = null;

    /**
     * Password for authentication.
     *
     * @var string
     */
    private $_password = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_db = Msd_Db::getAdapter();
        $config = Msd_Registry::getConfig();
        $tableConfig = $config->getParam('table');
        $dbUserConfig = $config->getParam('dbuser');
        $this->_database = $dbUserConfig['db'];
        $this->_users = $tableConfig['users'];
    }

    /**
     * Set the username, which is used for authentication.
     *
     * @param string $username Username
     *
     * @return void
     */
    public function setUsername($username)
    {
        $this->_username = (string) $username;
    }

    /**
     * Set the password, which is used for authentication.
     *
     * @param string $password Ther password
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->_password = (string) $password;
    }

    /**
     * Authenticate with the given credentials.
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $loginResult = true;
        $db = Msd_db::getAdapter();
        // look for user in own db-table
        $sql = 'SELECT `username` as `name`, `id`, `password` FROM `'.$this->_database . '`.`'.$this->_users.'` '
               .'WHERE `username`='
               .'\''.$db->escape($this->_username) . '\' AND `active`=1';

        $res = $db->query($sql, Msd_Db::ARRAY_ASSOC);
        if (!isset($res[0]['name'])) {
            // user not found in db
            $loginResult = false;
        } else {
            if ($res[0]['password'] != md5($this->_password)) {
                //TODO extract this to own model and make it configurable
                // admin should be able to glue this to any referring database
                // password incorrect - try to look into forum db
                $sql = 'SELECT `username` as `name`, `user_id` as `id` FROM `db81760001`.`phpbb_users` '
                       .'WHERE `username`='
                       .'\''.$db->escape($this->_username) . '\' AND `user_password`=\''
                       .(md5($this->_password)).'\' AND `msd_translator`=1';
                $res = $db->query($sql, Msd_Db::ARRAY_ASSOC);
                if (isset($res[0]['name'])) {
                    // log in correct - update own table
                    $loginResult = true;
                    $this->_updatePassword();
                } else {
                    // username exists but password doesn't match forum db
                    $loginResult = false;
                }
            }
        }

        if ($loginResult === true) {
            // user log in valid
            $authResult = array(
                'name' => $res[0]['name'],
                'id' => $res[0]['id'],
            );
            return new Zend_Auth_Result(
                Zend_Auth_Result::SUCCESS,
                $authResult
            );
        }

        // unknown user or password incorrect
        return new Zend_Auth_Result(
            Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
            array(),
            array('L_LOGIN_INVALID_USER')
        );
    }

    /**
     * Update password in own user table
     *
     * @return void
     */
    private function _updatePassword() {
        $sql = 'UPDATE `' . $this->_database .'`.`' . $this->_users
                . '` SET `password` = \'' . md5($this->_password) .'\'';
        $this->_db->query($sql);
    }
}

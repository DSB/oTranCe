<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Users
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Class for user login and logout actions.
 *
 * @package         MySQLDumper
 * @subpackage      Users
 */
class Msd_User
{
    /**
     * The executed process was successfully completed.
     *
     * @var int
     */
    const SUCCESS           = 0x00;

    /**
     * There is no file with user identities and credentials.
     *
     * @var int
     */
    const NO_USER_FILE      = 0x01;

    /**
     * The user file doesn't contain any valid user logins.
     *
     * @var int
     */
    const NO_VALID_USER     = 0x02;

    /**
     * The given identity is unknown or the password is wrong.
     *
     * @var int
     */
    const UNKNOWN_IDENTITY  = 0x03;

    /**
     * An unknown error occured.
     *
     * @var int
     */
    const GENERAL_FAILURE   = 0xFF;

    /**
     * Path and filename of the user ini file.
     *
     * @var string
     */
    private $_usersFile;

    /**
     * Instance to authentication storage.
     *
     * @var Zend_Auth_Storage_Session
     */
    private $_authStorage = null;

    /**
     * Id of currently loggedin user.
     *
     * @var int
     */
    private $_userId = null;

    /**
     * Name of currently loggedin user.
     *
     * @var string
     */
    private $_userName = null;

    /**
     * Current login status.
     *
     * @var boolean
     */
    private $_isLoggedIn = false;

    /**
     * Messages from Zend_Auth_Result.
     *
     * @var array
     */
    private $_authMessages = array();

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_authStorage = new Zend_Auth_Storage_Session();
        $auth = $this->_authStorage->read();
        if (!empty($auth)) {
            if (isset($auth['name'])) {
                $this->_userName = $auth['name'];
            }
            if (isset($auth['id'])) {
                $this->_userId = $auth['id'];
            }
            if ($this->_userName !== null && $this->_userId !== null) {
                $this->_isLoggedIn = true;
            }
        } else {
            $this->_loginByCookie();
        }
    }

    /**
     * Returns the messages which comes from Zend_Auth_Result.
     *
     * @return array
     */
    public function getAuthMessages()
    {
        return $this->_authMessages;
    }

    /**
     * Return the loggedin status.
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->_isLoggedIn;
    }

    /**
     * Login the user with the given identity and credentials.
     * Set cookie if automatic login is wanted.
     *
     * Returns true if login was successful, otherwise false.
     *
     * @param string  $username  Identity for login process.
     * @param string  $password  Credentials for login procress.
     * @param boolean $autoLogin Set cookie for automatic login?
     *
     * @return int
     */
    public function login($username, $password, $autoLogin = false)
    {
        $authAdapter = new Msd_Auth_Adapter_Db();
        $authAdapter->setUsername($username);
        $authAdapter->setPassword($password);
        $auth = Zend_Auth::getInstance();
        $authResult = $auth->authenticate($authAdapter);
        $this->_authMessages = $authResult->getMessages();
        if ($authResult->isValid()) {
            $this->_isLoggedIn = true;
            if ($autoLogin) {
                Zend_Session::regenerateId();
                $crypt = Msd_Crypt::getInstance('MySQLDumperTranslationCenter');
                $identity = $crypt->encrypt(
                    $username . ':' . $password
                );
                setcookie(
                    'oTranCe_autologin',
                    $identity . ':' . md5($identity),
                    time() + 365 * 24 * 60 * 60,
                    '/'
                );
            }
            $this->setDefaultConfiguration();
            return self::SUCCESS;
        }
        return self::UNKNOWN_IDENTITY;
    }

    private function _loginByCookie()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $cookie = $request->get('oTranCe_autologin');
        if ($cookie === null || $cookie == '') {
            // no cookie found
            return false;
        }
        list($authInfo, $checksum) = explode(':', $cookie);
        if (md5($authInfo) != $checksum) {
            // autologin not valid - return
            return false;
        }

        $crypt = Msd_Crypt::getInstance('oTranCe');
        list($username, $pass) = explode(':', $crypt->decrypt($authInfo));
        // Try to login the user and refresh the cookie. Because you want
        // to stay logged in until you logout.
        $this->login($username, $pass, true);
    }
    /**
     * Clear the user identity and logout the user.
     *
     * @return void
     */
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_isLoggedIn = false;
        $this->setDefaultConfiguration();
    }

    /**
     * Set default configuration for user
     *
     *  @return void
     */
    public function setDefaultConfiguration()
    {
        if ($this->_isLoggedIn) {
            $files = Msd_File::getConfigNames();
            if (isset($files[0])) {
                $config = Msd_Configuration::getInstance($files[0], true);
                return;
            }
        }
        $config = Msd_Configuration::getInstance('defaultConfig', true);
    }
}

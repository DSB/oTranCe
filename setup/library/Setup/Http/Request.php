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
 * Class for managing HTTP requests.
 *
 * @package         MySQLDumper
 * @subpackage      Archive
 */
class Setup_Http_Request
{
    /**
     * URL parameter name used to detect the requested controller name.
     *
     * @var string
     */
    protected $_controllerKey = 'controller';

    /**
     * URL parameter name used to detect the requested action name.
     *
     * @var string
     */
    protected $_actionKey = 'action';

    /**
     * Current requested controller.
     *
     * @var string
     */
    protected $_controller;

    /**
     * Current requested action.
     *
     * @var string
     */
    protected $_action;

    /**
     * Initializes the request.
     *
     * @param string $controllerKey Name of the URL parameter for the controller name.
     * @param string $actionKey     Name of the URL parameter for the action name.
     *
     * @return \Setup_Http_Request
     */
    public function __construct($controllerKey = null, $actionKey = null)
    {
        if ($controllerKey !== null) {
            $this->setControllerKey($controllerKey);
        }

        if ($actionKey !== null) {
            $this->setActionKey($actionKey);
        }

        $this->setController($this->getParam($this->getControllerKey(), 'index'));
        $this->setAction($this->getParam($this->getActionKey(), 'index'));
    }

    /**
     * Retrieves an URL parameter.
     *
     * @param string $name    Name of the URL parameter,
     * @param mixed  $default Default value if the parameter doesn't exists.
     *
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        switch (true) {
            case isset($_GET[$name]):
                return $_GET[$name];
            case isset($_POST[$name]):
                return $_POST[$name];
            case isset($_COOKIE[$name]):
                return $_COOKIE[$name];
            case isset($_REQUEST[$name]):
                return $_REQUEST[$name];
            case isset($_FILES[$name]):
                return $_FILES[$name];
            case isset($_SESSION[$name]):
                return $_SESSION[$name];
            case isset($_SERVER[$name]):
                return $_SERVER[$name];
            case isset($_ENV[$name]):
                return $_ENV[$name];
            default:
                return $default;
        }
    }

    /**
     * Sets the name of the requested action.
     *
     * @param string $action Name of the requested action.
     *
     * @return void
     */
    public function setAction($action)
    {
        $this->_action = (string)$action;
    }

    /**
     * Retrieves the name of the requested Action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Sets the URL parameter name for the requested action.
     *
     * @param string $actionKey URL parameter name for the requested action.
     *
     * @return void
     */
    public function setActionKey($actionKey)
    {
        $this->_actionKey = (string)$actionKey;
    }

    /**
     * Retrieves the URL parameter name for the requested action.
     *
     * @return string
     */
    public function getActionKey()
    {
        return $this->_actionKey;
    }

    /**
     * Sets the name of the requested controller.
     *
     * @param string $controller Name of the requested controller.
     *
     * @return void
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
    }

    /**
     * Sets the name of the requested controller.
     *
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Sets the URL parameter name for the requested controller.
     *
     * @param string $controllerKey URL parameter name for the requested controller.
     *
     * @return void
     */
    public function setControllerKey($controllerKey)
    {
        $this->_controllerKey = $controllerKey;
    }

    /**
     * Retrieves the URL parameter name for the requested controller.
     *
     * @return string
     */
    public function getControllerKey()
    {
        return $this->_controllerKey;
    }
}

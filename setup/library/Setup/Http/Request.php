<?php
class Setup_Http_Request
{
    protected $controllerKey = 'controller';

    protected $actionKey = 'action';

    protected $controller;

    protected $action;

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

    public function setAction($action)
    {
        $this->action = (string)$action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setActionKey($actionKey)
    {
        $this->actionKey = (string)$actionKey;
    }

    public function getActionKey()
    {
        return $this->actionKey;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setControllerKey($controllerKey)
    {
        $this->controllerKey = $controllerKey;
    }

    public function getControllerKey()
    {
        return $this->controllerKey;
    }
}

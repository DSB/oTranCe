<?php
/**
 * Class for dynamic (session lifetime) configuration settings.
 */
class Msd_Config_Dynamic
{
    /**
     * Instance of Zend_Session_Namespace for session storage.
     *
     * @var Zend_Session_Namespace
     */
    private $_namespace = null;

    /**
     * Class constructor.
     *
     * @param string $sessionNsName Name of the session namespace.
     *
     * @return Msd_Config_Dynamic
     */
    public function __construct($sessionNsName = 'Dynamic')
    {
        $this->_namespace = new Zend_Session_Namespace($sessionNsName);
    }

    /**
     * Retrieves the value of a parameter.
     *
     * @param string $name    Name of the parameter.
     * @param mixed  $default Default value to return, if param isn't set.
     * 
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if (isset($this->_namespace->$name)) {
            return $this->_namespace->$name;
        }

        return $default;
    }

    /**
     * Sets a value for the given parameter.
     *
     * @param string $name  Name of the parameter.
     * @param mixed  $value Value for the parameter.
     * 
     * @return void
     */
    public function setParam($name, $value)
    {
        $this->_namespace->$name = $value;
    }
}

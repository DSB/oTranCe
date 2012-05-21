<?php
class Setup_View
{
    /**
     * Path to the view scripts.
     *
     * @var string
     */
    protected $_scriptPath;

    /**
     * Data for the view script.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Initializes the view.
     *
     * @param string $scriptPath Path to the view script directory.
     *
     * @return \Setup_View
     */
    public function __construct($scriptPath)
    {
        $this->_scriptPath = $scriptPath;
    }

    /**
     * Renders a view script.
     *
     * @param string $viewScript Name of the view-script to render.
     *
     * @return string
     */
    public function render($viewScript)
    {
        ob_start();
        include $this->_scriptPath . '/' . $viewScript;
        return ob_get_clean();
    }

    /**
     * Sets a variable for the view script.
     *
     * @param string $key   Name of the variable.
     * @param mixed  $value Value of the variable.
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Retrieves the value of a view variable.
     *
     * @param string $key Name of the variable.
     *
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Tests the existence of a view script variable.
     *
     * @param string $key Name of the variable.
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }
}

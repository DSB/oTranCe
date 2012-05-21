<?php
class Setup_Application
{
    /**
     * Application configuration.
     *
     * @var array
     */
    protected $_config;

    /**
     * Instance of the request object.
     *
     * @var Setup_Http_Request
     */
    protected $_request;

    /**
     * Instance of the response object.
     *
     * @var Setup_Http_Response
     */
    protected $_response;

    /**
     * Path to the controller directory.
     *
     * @var string
     */
    protected $_controllerDir = 'controllers/';

    /**
     * Initializes instance of the application.
     *
     * @param string|array $config Filename or array of the application configuration.
     *
     * @return Setup_Application
     */
    public function __construct($config)
    {
        if (is_string($config) && file_exists($config)) {
            $config = parse_ini_file($config, true);
        }

        if (is_array($config)) {
            $this->setConfig($config);
        }

        $this->init();
    }

    /**
     * Initializes the application.
     *
     * @return void
     */
    public function init()
    {
        if (isset($this->_config['application']['controllerDir'])) {
            $sanatizedControllerDir = rtrim($this->_config['application']['controllerDir'], '/\\') . '/';
            $this->setControllerDir($sanatizedControllerDir);
        }

        $this->initRequest();
        $this->initResponse();
    }

    /**
     * Initializes the request object.
     *
     * @return void
     */
    protected function initRequest()
    {
        $controllerKey = null;
        $actionKey = null;
        $config = $this->getConfig();

        if (isset($config['request']['controllerKey'])) {
            $controllerKey = $config['request']['controllerKey'];
        }

        if (isset($config['request']['actionKey'])) {
            $actionKey = $config['request']['actionKey'];
        }

        $request = new Setup_Http_Request($controllerKey, $actionKey);
        $this->setRequest($request);
    }

    /**
     * Initializes response object.
     *
     * @return void
     */
    protected function initResponse()
    {
        $response = new Setup_Http_Response();
        $this->setResponse($response);
    }

    /**
     * Loads the controller and executes the action.
     *
     * @throws Setup_Application_Exception
     *
     * @return void
     */
    public function run()
    {
        /**
         * @var Setup_Controller_Abstract $controllerInstance
         */

        $request = $this->getRequest();
        $controller = $request->getController();

        $controllerName = ucfirst($controller) . "Controller";
        $controllerFilename = $controllerName . '.php';

        $controllerFile = $this->getControllerDir() . $controllerFilename;
        if (!file_exists($controllerFile)) {
            require_once 'Setup/Application/Exception.php';
            throw new Setup_Application_Exception("Can't load controller '$controllerName'!");
        }

        include_once $controllerFile;
        if (!class_exists($controllerName)) {
            require_once 'Setup/Application/Exception.php';
            throw new Setup_Application_Exception("Unknown controller '$controllerName' in '$controllerFilename'!");
        }

        $controllerInstance = new $controllerName($this->_request, $this->_response, $this->_config['setup']);
        if (!$controllerInstance instanceof Setup_Controller_Abstract) {
            require_once 'Setup/Application/Exception.php';
            throw new Setup_Application_Exception(
                "Controller '$controllerName' must extend 'Setup_Controller_Abstract'!"
            );
        }

        $actionName = $request->getAction() . 'Action';
        if (!is_callable(array($controllerInstance, $actionName))) {
            require_once 'Setup/Application/Exception.php';
            throw new Setup_Application_Exception(
                "Controller '$controllerName' does not provide '$actionName'!"
            );
        }

        $view = new Setup_View($this->getControllerDir() . '/../views');
        $controllerInstance->setView($view);

        ob_start();
        $controllerInstance->$actionName();
        $controllerOutput = ob_get_clean();
        $this->_response->prependBody($controllerOutput);

        $body = $this->_response->getBody();
        if (empty($body)) {
            $output = $view->render($this->_request->getController() . '/' . $this->_request->getAction() . '.phtml');
            $this->_response->setBody($output);
        }

        $this->_response->sendResponse();
    }

    /**
     * Sets the application configuration.
     *
     * @param array $config Application configuration.
     *
     * @return void
     */
    public function setConfig($config)
    {
        $this->_config = (array) $config;
    }

    /**
     * Retrieves the application configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Sets the instance of the request object.
     *
     * @param \Setup_Http_Request $request Instance of the request object.
     *
     * @return void
     */
    public function setRequest($request)
    {
        $this->_request = $request;
    }

    /**
     * Retrieves the instance of the request object.
     *
     * @return Setup_Http_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieves the path for the controller directory.
     *
     * @param string $controllerDir Path to the controller directory.
     *
     * @return void
     */
    public function setControllerDir($controllerDir)
    {
        $this->_controllerDir = $controllerDir;
    }

    /**
     * Sets the path for the controller directory.
     *
     * @return string
     */
    public function getControllerDir()
    {
        return $this->_controllerDir;
    }

    /**
     * Sets the instance of the response object.
     *
     * @param \Setup_Http_Response $response Instance of the response object.
     *
     * @return void
     */
    public function setResponse($response)
    {
        $this->_response = $response;
    }

    /**
     * Retrieves the instance of the response object.
     *
     * @return \Setup_Http_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}

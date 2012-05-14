<?php
class Setup_Application
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Setup_Http_Request
     */
    protected $request;

    /**
     * @var Setup_Http_Response
     */
    protected $response;

    protected $controllerDir = 'controllers/';

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

    public function init()
    {
        if (isset($this->config['application']['controllerDir'])) {
            $sanatizedControllerDir = rtrim($this->config['application']['controllerDir'], '/\\') . '/';
            $this->setControllerDir($sanatizedControllerDir);
        }

        $this->initRequest();
        $this->initResponse();
    }

    protected function initRequest()
    {
        $controllerKey = null;
        $actionKey = null;
        $order = null;
        $config = $this->getConfig();

        if (isset($config['request']['controllerKey'])) {
            $controllerKey = $config['request']['controllerKey'];
        }

        if (isset($config['request']['actionKey'])) {
            $actionKey = $config['request']['actionKey'];
        }

        if (isset($config['request']['order'])) {
            $order = $config['request']['order'];
        }

        $request = new Setup_Http_Request($controllerKey, $actionKey, $order);
        $this->setRequest($request);
    }

    protected function initResponse()
    {
        $response = new Setup_Http_Response();
        $this->setResponse($response);
    }

    public function run()
    {
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

        $controllerInstance = new $controllerName($this->request, $this->response);
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

        $controllerInstance->$actionName();
    }

    public function setConfig($config)
    {
        $this->config = (array)$config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \Setup_Http_Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return Setup_Http_Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function setControllerDir($controllerDir)
    {
        $this->controllerDir = $controllerDir;
    }

    public function getControllerDir()
    {
        return $this->controllerDir;
    }

    /**
     * @param \Setup_Http_Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return \Setup_Http_Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}

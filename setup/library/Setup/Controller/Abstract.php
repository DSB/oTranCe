<?php
abstract class Setup_Controller_Abstract
{
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
     * Configuration for the controllers.
     *
     * @var array
     */
    protected $_config;

    /**
     * Initializes the controller.
     *
     * @param Setup_Http_Request  $request  Request object.
     * @param Setup_Http_Response $response Response object.
     * @param array               $config   Application configuration.
     *
     * @return \Setup_Controller_Abstract
     */
    public function __construct(Setup_Http_Request $request, Setup_Http_Response $response, $config)
    {
        $this->setRequest($request);
        $this->setResponse($response);
        $this->setConfig($config);

        $this->init();
    }

    /**
     * Method for controller specific initialization code. Will be overwritten by the controller.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Sets the request object.
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
     * Retrieves the request object.
     *
     * @return \Setup_Http_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Sets the response object.
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
     * Retrieves the response object.
     *
     * @return \Setup_Http_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Sets application configuration.
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
}

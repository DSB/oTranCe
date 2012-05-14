<?php
abstract class Setup_Controller_Abstract
{
    /**
     * @var Setup_Http_Request
     */
    protected $request;

    /**
     * @var Setup_Http_Response
     */
    protected $response;

    public function __construct(Setup_Http_Request $request, Setup_Http_Response $response)
    {
        $this->setRequest($request);
        $this->setResponse($response);

        $this->init();
    }

    public function init()
    {
    }

    /**
     * @param \Setup_Http_Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Setup_Http_Request
     */
    public function getRequest()
    {
        return $this->request;
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

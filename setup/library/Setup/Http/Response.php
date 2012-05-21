<?php
class Setup_Http_Response
{
    /**
     * Response body
     *
     * @var string
     */
    protected $_body;

    /**
     * Response headers
     * @var array
     */
    protected $_headers = array();

    /**
     * Sets a response header.
     *
     * @param string $header Name of the response header.
     * @param string $value  Value for the response header.
     *
     * @return void
     */
    public function setHeader($header, $value)
    {
        $this->_headers[$header] = $value;
    }

    /**
     * Removes a response header
     *
     * @param string $header Name of the response header.
     *
     * @return void
     */
    public function removeHeader($header)
    {
        unset($this->_headers[$header]);
    }

    /**
     * Removes all response headers.
     *
     * @return void
     */
    public function clearHeaders()
    {
        $this->_headers = array();
    }

    /**
     * Sets the response body
     *
     * @param string $body Response body to set.
     *
     * @return void
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }

    /**
     * Retrieves the response body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Prepends output to the response header.
     *
     * @param string $body Output to prepend.
     *
     * @return void
     */
    public function prependBody($body)
    {
        $this->_body = $body . $this->_body;
    }

    /**
     * Sets the response body for a JSON response. It also modifies the response header.
     *
     * @param mixed $data Data for the response, it will encoded by json_encode function.
     *
     * @return void
     */
    public function setBodyJson($data)
    {
        $this->setBody(json_encode($data));
        $this->setHeader('Content-Type', 'application/json');
    }

    /**
     * Sends the entire response.
     *
     * @return void
     */
    public function sendResponse()
    {
        $this->sendHeaders();
        $this->sendBody();
    }

    /**
     * Sends the response headers, if they aren't already sent.
     *
     * @return bool
     */
    public function sendHeaders()
    {
        if (headers_sent()) {
            return false;
        }

        foreach ($this->_headers as $header => $value) {
            header("$header: $value", true);
        }

        return true;
    }

    /**
     * Sends the entiry response body.
     *
     * @return void
     */
    public function sendBody()
    {
        echo $this->_body;
    }
}

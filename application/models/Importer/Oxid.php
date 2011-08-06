<?php

class Application_Model_Importer_Oxid extends Application_Model_Importer_PhpArray
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_ignoreKeys = array('charset');
    }
}

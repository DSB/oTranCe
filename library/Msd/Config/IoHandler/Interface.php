<?php
interface Msd_Config_IoHandler_Interface
{
    public function __construct($handlerOptions = array());
    public function load($configFilename);
    public function save($config);
}

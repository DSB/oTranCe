<?php
/**
 * Importer class for OXID language files.
 */
class Msd_Import_Oxid extends Msd_Import_PhpArray
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_ignoreKeys = array('charset');
    }
}

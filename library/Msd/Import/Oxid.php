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

    /**
     * Get rendered info view
     *
     * @param Zend_View $view View instance
     *
     * @return string
     */
    public function getInfo(Zend_View $view)
    {
        return $view->render('import/importer/oxid.phtml');
    }

}

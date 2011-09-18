<?php
/**
 * Importer class for OXID language files.
 */
class Module_Import_Oxid extends Msd_Import_PhpArray
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
        $path = realpath(APPLICATION_PATH . '/../modules/library/Import/views') . DS;
        $view->addScriptPath($path);
        return $view->render('oxid.phtml');
    }

}

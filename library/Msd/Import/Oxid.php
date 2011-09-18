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
     * @return string
     */
    public function getInfo()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $view = $viewRenderer->view;
        return $view->render('import/importer/oxid.phtml');
    }

}

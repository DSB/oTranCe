<?php
/**
 * This file is part of oTranCe released under the GNU GPL 3 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Importer
 * @version         SVN: $
 * @author          $Author: $
 */

/**
 * Importer for OXID language files.
 *
 * @package         oTranCe
 * @subpackage      Importer
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
        return $view->render('oxid.phtml');
    }

}

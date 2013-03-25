<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://www.otrance.org
 *
 * @package         oTranCe
 * @subpackage      Plugins
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Check if application is invoked via cli call and disable MVC and view renderer
 *
 * @package         oTranCe
 * @subpackage      Plugins
 */
class Application_Plugin_DisableLayout extends Zend_Controller_Plugin_Abstract
{
    /**
     * Method will disable the MVC and view renderer if app is called via cli
     *
     * @param Zend_Controller_Request_Abstract $request The request object
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
    }

}

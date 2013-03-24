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
 * Check if user switched the language and save change to user config
 *
 * @package         oTranCe_Plugins
 * @subpackage      LoginCheck
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
        if (PHP_SAPI == 'cli') {
            Zend_Layout::getMvcInstance()->disableLayout();
            Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        }
    }

}

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
 * Check if application is invoked via cli call. Then disable MVC and add cli-views to view renderer.
 *
 * @package         oTranCe
 * @subpackage      Plugins
 */
class Application_Plugin_DisableLayout extends Zend_Controller_Plugin_Abstract
{
    /**
     * Method will disable the MVC and view renderer if app is called via cli.
     * Whether this plug in is active or not is decided in Bootstrap::_initRouter.
     *
     * @param Zend_Controller_Request_Abstract $request The request object
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // disable layout rendering (header, menu, ect.)
        Zend_Layout::getMvcInstance()->disableLayout();
        // add "views/cli" to script path - if no cli-view can be found, we fall back to normal html view
        $view       = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;
        $scriptPath = sprintf('%s/views/cli', APPLICATION_PATH);
        $view->addScriptPath($scriptPath);
    }

}

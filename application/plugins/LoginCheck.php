<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Plugins
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Check log in of user and redirect to log in form if user is not logged in.
 *
 * @package         MySQLDumper_Plugins
 * @subpackage      LoginCheck
 */
class Application_Plugin_LoginCheck extends Zend_Controller_Plugin_Abstract
{
    /**
     * Method will be executed befor the dispatch process starts.
     *
     * @param Zend_Controller_Request_Abstract $request
     *
     * @return
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $controllerName = $request->getControllerName();
        if (
            ($request->getActionName() == 'login' &&
            $controllerName == 'index') ||
            $controllerName == 'error'
        ) {
            return;
        }
        $user = new Msd_User();
        if (!$user->isLoggedIn()) {
            // redirect to login form if user is not logged in
            $frontController = Zend_Controller_Front::getInstance();
            $view = new Zend_View;
            $fullUrl = 'http://' . $request->getHttpHost() . $view->baseUrl() .'/index/login/';
            $frontController->getResponse()->setRedirect($fullUrl);
        }
    }

}

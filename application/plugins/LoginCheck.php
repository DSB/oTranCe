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
 * Check log in of user and redirect to log in form if user is not logged in.
 *
 * @package         oTranCe_Plugins
 * @subpackage      LoginCheck
 */
class Application_Plugin_LoginCheck extends Zend_Controller_Plugin_Abstract
{
    /**
     * Method will be executed before the dispatch process starts.
     *
     * @param Zend_Controller_Request_Abstract $request The request object
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        if ($this->_isLoginPage() || $this->_isRegisterPage() || $this->_isErrorPage()) {
            return;
        }

        $user = new Msd_User();
        if (!$user->isLoggedIn()) {
            // redirect to login form if user is not logged in
            $frontController = Zend_Controller_Front::getInstance();
            $view            = new Zend_View;
            $fullUrl         = $view->serverUrl() . $view->baseUrl() .'/index/login/';
            $frontController->getResponse()->setRedirect($fullUrl);
        }
    }

    /**
     * Returns true, if the login page is requested.
     *
     * @return bool
     */
    protected function _isLoginPage()
    {
        return ($this->_request->getActionName() == 'login' && $this->_request->getControllerName() == 'index');
    }

    /**
     * Returns true, if the register page is requested.
     *
     * @return bool
     */
    protected function _isRegisterPage()
    {
        $controllerName = $this->_request->getControllerName();
        return (in_array($controllerName, array('register', 'index')));
    }

    /**
     * Returns true, if the error page is requested.
     *
     * @return bool
     */
    protected function _isErrorPage()
    {
        return ($this->_request->getControllerName() == 'error');
    }
}

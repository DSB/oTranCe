<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Error Controller
 *
 * Handle unexpected errors and uncaught Exceptions
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * Handle error
     *
     * @return void
     */
    public function errorAction()
    {
        $this->_helper->layout->disableLayout();
        $errors = $this->_getParam('error_handler');
        if (is_object($errors)) {
            $exceptionTypes = array(
                Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE,
                Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER,
                Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION
            );
            if (in_array($errors->type, $exceptionTypes)) {
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
            } else {
                $this->_setDisplayError();
                // application error
                $this->getResponse()->setHttpResponseCode(200);
                $this->view->message = 'Application error';
            }

            $this->view->exception = $errors->exception;
            $this->view->request   = $errors->request;
        }
    }

    /**
     * Sets display errors if user has admin right or if we are in testing or development environment
     *
     * @return void
     */
    public function _setDisplayError()
    {
        $this->view->displayErrors = 0;
        $this->_userModel          = new Application_Model_User();
        if ($this->_userModel->hasRight('admin')) {
            $this->view->displayErrors = 1;
        }

        if (in_array(APPLICATION_ENV, array('development', 'testing'))) {
            $this->view->displayErrors = 1;
        }
    }

    /**
     * Show error message "no file template configured"
     *
     * @return void
     */
    public function noFileTemplateAction()
    {
    }

    /**
     * Show error message "no permission for this action"
     *
     * @return void
     */
    public function notAllowedAction()
    {
        $projectConfig            = $this->view->config->getParam('project');
        $activeProject            = $this->view->dynamicConfig->getParam('activeProject');
        $projectConfig            = $projectConfig[$activeProject];
        $this->view->projectEmail = $projectConfig['email'];
    }
}


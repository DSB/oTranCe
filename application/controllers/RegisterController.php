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
 * Register Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class RegisterController extends Zend_Controller_Action
{
    /**
     * Register a user
     *
     * @return void
     */
    public function indexAction()
    {
        $default  = array(
            'id'       => 0,
            'active'   => 0,
            'username' => '',
            'pass1'    => '',
            'pass2'    => '',
        );
        $userData = $this->_request->getParam('user', $default);
        if (!empty($userData['username'])) {
            $userModel          = new Application_Model_User();
            $userData['id']     = 0;
            $userData['active'] = 0;

            if ($userModel->validateData($userData, $this->view->lang->getTranslator())) {
                $userModel->saveAccount($userData);
                $this->view->registerSuccess = true;
            } else {
                $this->view->errors = $userModel->getValidateMessages();
            }
        }
        $this->view->isLogin               = true;
        $this->view->request               = $this->_request;
        $this->view->user                  = $userData;
        $this->view->availableGuiLanguages = $this->view->dynamicConfig->getParam('availableGuiLanguages');
    }
}

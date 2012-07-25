<?php
require_once 'SettingsController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers_Settings
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Settings Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Settings
 */
class Settings_ProfileController extends SettingsController
{
    /**
     * @var Application_Model_User
     */
    protected $_userModel;

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        if ($this->_request->isPost()) {
            $oldPassword = $this->_request->getParam('oldPassword');
            $newPassword = $this->_request->getParam('newPassword');
            $newPasswordConfirm = $this->_request->getParam('newPasswordConfirm');
            $this->_changePassword($oldPassword, $newPassword, $newPasswordConfirm);
        }
    }

    /**
     * Helper method for changing the new user password.
     *
     * @param string $oldPassword        Old password.
     * @param string $newPassword        New password.
     * @param string $newPasswordConfirm Password confirmation.
     *
     * @return void
     */
    protected function _changePassword($oldPassword, $newPassword, $newPasswordConfirm)
    {
        $translator = $this->view->lang;
        $auth = Zend_Auth::getInstance()->getIdentity();
        $user = $this->_userModel->getUserById($auth['id']);
        if (empty($oldPassword) || md5($oldPassword) != $user['password']) {
            $this->view->errors = array(
                'oldPass' => array(0 => $translator->translate('L_PROVIDED_PASSWORD_IS_WRONG'))
            );
            $this->view->saved = false;
            return;
        }

        $user['pass1'] = $newPassword;
        $user['pass2'] = $newPasswordConfirm;
        if ($this->_userModel->validateData($user, $translator, true)) {
            $res = $this->_userModel->saveAccount($user);
            if ($res !== false) {
                $this->view->saved = true;
                $cookie = $this->_request->getCookie('oTranCe_autologin');
                if ($cookie !== null && !empty($cookie)) {
                    $user = new Msd_User();
                    $user->setLoginCookie($auth['name'], $newPassword);
                }
            }
        } else {
            $this->view->saved = false;
            $this->view->errors = $this->_userModel->getValidateMessages();
        }
    }
}

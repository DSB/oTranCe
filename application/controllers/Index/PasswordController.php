<?php
require_once('IndexController.php');
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */

/**
 * Forgot password Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class Index_PasswordController extends IndexController
{
    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $form = new Application_Form_ForgotPassword();

        $this->view->assign(
            array(
                 'availableGuiLanguages' => $this->view->dynamicConfig->getParam('availableGuiLanguages'),
                 'request'               => $this->_request,
                 'form'                  => $form,
                 'isLogin'               => true,
            )
        );

    }

    /**
     * Request password action
     *
     * @return void
     */
    public function requestPasswordAction()
    {
        $languagesModel    = new Application_Model_Languages();
        $languagesMetaData = $languagesModel->getAllLanguages();
        $userName          = $this->getRequest()->getParam('username');
        $translator        = Msd_Language::getInstance();
        $user              = new \Application_Model_User();
        $userExists        = $user->userNameExists($userName);

        if (!$userExists) {
            $errorMsg            = $translator->translate('L_FORGOT_PASSWORD_USERNAME_NOT_EXISTS');
            $errorMsg            = sprintf($errorMsg, $userName);
            $this->view->isError = true;
            $this->setViewNotifications($errorMsg);
        }

        if ($userExists) {
            $user     = new Application_Model_User();
            $userData = $user->getUserByName($userName);

            //-- check if user mail exists
            if (isset($userData['id'])) {
                //-- generate mail link
                $forgotPasswordModel = new Application_Model_ForgotPassword();

                //-- store request
                if ($forgotPasswordModel->saveRequest($userData['id'])) {
                    $forgotPasswordModel->setLinkHashId($userData);
                    $link = '/index_password/resetpassword/id/' . $forgotPasswordModel->getGeneratedHashId();

                    //-- send email
                    $mailer = new Application_Model_Mail($this->view);
                    $mailer->sendForgotPasswordMail($userData, $languagesMetaData, $link);

                    $this->view->isError = false;
                    $this->setViewNotifications(null, $translator->translate('L_FORGOT_PASSWORD_SEND_MAIL'));
                }
            } else {
                $this->view->isError = true;
                $this->setViewNotifications($translator->translate('L_FORGOT_PASSWORD_UNKNOWN_USER'));
            }
        }

        $this->forward('index', 'index_password');
    }

    /**
     * Sets different message for displaying
     *
     * @param string|null $errorMessage   Error message to assign
     * @param string|null $successMessage Success message to assign
     *
     * @return void
     */
    protected function setViewNotifications($errorMessage = null, $successMessage = null)
    {
        if ($errorMessage === null) {
            $errorMessage = '';
        }

        if ($successMessage === null) {
            $successMessage = '';
        }

        $params = array(
            'infos' => array(
                'SUCCESS_MESSAGE' => $successMessage,
                'ERROR_MESSAGE'   => $errorMessage,
            )
        );

        $this->view->assign($params);
    }

    /**
     * Reset password action
     *
     * @return void
     */
    public function resetpasswordAction()
    {
        $queryData = base64_decode($this->getRequest()->getParam('id'));
        parse_str($queryData, $params);

        $forgotPasswordModel = new Application_Model_ForgotPassword();
        $translator          = Msd_Language::getInstance();
        if (!isset($params['id'], $params['userid'], $params['usermail'], $params['timestamp'])) {
            // link not correct, silently forward to index page
            $this->forward('index', 'index_password');
        }

        if (!$forgotPasswordModel->isValidRequest($params['id'], $params['userid'], $params['timestamp'])) {
            $this->view->isError = true;
            $this->setViewNotifications($translator->translate('L_FORGOT_PASSWORD_EXPIRED_LINK'));
            $this->forward('index', 'index_password');
        }

        $this->view->assign(
            array(
                 'availableGuiLanguages' => $this->view->dynamicConfig->getParam('availableGuiLanguages'),
                 'request'               => $this->_request,
                 'userid'                => $params['userid'],
                 'userhash'              => $this->getRequest()->getParam('id'),
                 'isLogin'               => true,
            )
        );
    }

    /**
     * Sets new password for a single user
     *
     * @return void
     */
    public function setpasswordAction()
    {
        $this->clearRedirectAfterLogin();
        $translator = Msd_Language::getInstance();
        $password   = $this->_request->getParam('user_password');
        $confirmPwd = $this->_request->getParam('user_password2');
        $userId     = $this->_request->getParam('userid');
        $user       = new Application_Model_User();

        $params = array('id' => $this->_request->getParam('userhash'));

        $userData = array(
            'pass1' => $password,
            'pass2' => $confirmPwd
        );

        if (!$user->validateData($userData, $translator, true)) {
            $messages = $user->getValidateMessages();
            $msg      = '';

            foreach ($messages['pass1'] as $validatedField => $validateMsg) {
                $msg .= $validateMsg . '<br>';
            }

            $this->view->isError = true;
            $this->setViewNotifications($msg);

            $this->forward('resetpassword', 'index_password', '', $params);
        } else {

            if ($user->setPassword($userId, $password)) {
                $forgotPassword = new Application_Model_ForgotPassword();
                $forgotPassword->deleteRequestByUserId($userId);

                $this->view->isError = false;
                $this->setViewNotifications(null, $translator->translate('L_SET_PASSWORD_SUCCESS'));

                $this->forward('index', 'index');
            }
        }
    }

    /**
     * Don't redirect to password actiosn after login
     */
    protected function clearRedirectAfterLogin()
    {
        $ns = new Zend_Session_Namespace('requestData');
        $ns->unsetAll();
    }
}

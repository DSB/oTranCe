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
    public function indexAction()
    {
        $form = new Application_Form_ForgotPassword();

        $this->view->assign(
            array(
                'availableGuiLanguages' => $this->view->dynamicConfig->getParam('availableGuiLanguages'),
                'request' => $this->_request,
                'form' => $form,
                'isLogin'               => true,
            )
        );

    }

    public function requestpasswordAction()
    {
        $languagesModel = new Application_Model_Languages();
        $languagesMetaData = $languagesModel->getAllLanguages();
        $userEmail = $this->getRequest()->getParam('user_email');
        $emailValidator = new Zend_Validate_EmailAddress();
        $translator = Msd_Language::getInstance();
        $isValidEmail = $emailValidator->isValid($userEmail);

        if (!$isValidEmail) {
            $errorMsg = array_shift($translator->translateZendMessageIds($emailValidator->getMessages()));
            $this->view->isError = true;
            $this->setViewNotifications($errorMsg);
        }

        if($isValidEmail){
            $user = new Application_Model_User();
            $userModel = $user->getUserByEmail($userEmail);

            //-- check if user mail exists
            if ($userModel) {
                //-- generate mail link

                $forgotPasswordModel = new Application_Model_ForgotPassword();

                //-- store request
                if ($forgotPasswordModel->saveRequest($userModel['id'])) {
                    $forgotPasswordModel->setLinkHashId($userModel);

                    $link = '/index_password/resetpassword/id/' . $forgotPasswordModel->getGeneratedHashId();

                    //-- send email
                    $mailer = new Application_Model_Mail($this->view);
                    $mailer->sendForgotPasswordMail($userModel, $languagesMetaData, $link);


                    $this->view->isError = false;
                    $this->setViewNotifications(null, $translator->translate('L_FORGOT_PASSWORD_SEND_MAIL'));

                }
            } else {
                $this->view->isError = true;
                $this->setViewNotifications($translator->translate('L_FORGOT_PASSWORD_UNKNOWN_USER'));
            }
        }

        $this->_forward('index', 'index_password');
    }

    /**
     * Sets different message for displaying
     *
     * @param string|null $errorMessage
     * @param string|null $successMessage
     */
    protected function setViewNotifications($errorMessage = null, $successMessage = null)
    {
        if($errorMessage === null){
            $errorMessage = '';
        }

        if($successMessage === null){
            $successMessage = '';
        }

        $params = array(
            'infos' => array(
                'SUCCESS_MESSAGE' => $successMessage,
                'ERROR_MESSAGE' => $errorMessage,
            )
        );

        $this->view->assign($params);

    }

    public function resetpasswordAction()
    {
        $userHash = base64_decode($this->getRequest()->getParam('id'));
        $paramArray = $this->getParamsFromHash($userHash);
        $forgotPasswordModel = new Application_Model_ForgotPassword();
        $translator = Msd_Language::getInstance();

        if (!$forgotPasswordModel->isValidRequest($paramArray['id'], $paramArray['userid'])) {
            $this->view->isError = true;
            $this->setViewNotifications($translator->translate('L_FORGOT_PASSWORD_EXPIRED_LINK'));
            $this->_forward('index', 'index_password');
        }

        $this->view->assign(
            array(
                'availableGuiLanguages' => $this->view->dynamicConfig->getParam('availableGuiLanguages'),
                'request' => $this->_request,
                'userid' => $paramArray['userid'],
                'userhash' => $this->getRequest()->getParam('id'),
                'isLogin'               => true,
            )
        );
    }

    /**
     * splits hash into his params and returns them back
     *
     * @param string $hash
     * @return array
     */
    protected function getParamsFromHash($hash)
    {
        $mainParams = explode('&', $hash);

        foreach ($mainParams as $params) {
            $tmpParams = explode('=', $params);

            $realParams[$tmpParams[0]] = $tmpParams[1];
        }

        return $realParams;
    }

    /**
     * Sets new password for a single user
     */
    public function setpasswordAction()
    {
        $translator = Msd_Language::getInstance();
        $password = $this->_request->getParam('user_password');
        $confirm_pwd = $this->_request->getParam('user_password2');
        $userid = $this->_request->getParam('userid');
        $user = new Application_Model_User();

        $params = array(
            'id' => $this->_request->getParam('userhash')
        );

        $userData = array(
            'pass1' => $password,
            'pass2' => $confirm_pwd
        );

        if(!$user->validateData($userData, $translator, true)){
            $messages = $user->getValidateMessages();
            $msg = '';

            foreach($messages['pass1'] as $validatedField => $validateMsg){
                $msg .= $validateMsg . '<br>';
            }

            $this->view->isError = true;
            $this->setViewNotifications($msg);

            $this->_forward('resetpassword', 'index_password', '', $params);
        } else {

            if($user->setPassword($userid, $password)){
                $forgotPassword = new Application_Model_ForgotPassword();
                $forgotPassword->deleteRequestByUserId($userid);

                $this->view->isError = false;
                $this->setViewNotifications(null, $translator->translate('L_SET_PASSWORD_SUCCESS'));

                $this->_forward('index', 'index');
            }
        }

    }
}

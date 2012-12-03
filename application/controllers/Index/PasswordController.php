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
        $this->view->assign(
            array(
                'availableGuiLanguages' => $this->view->dynamicConfig->getParam('availableGuiLanguages'),
                'request' => $this->_request
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

        echo "<PRE>";

        var_dump($userEmail);
        if (!$emailValidator->isValid($userEmail)) {

            var_dump($emailValidator->getMessages());

            $errors['email'] = $translator->translate($emailValidator->getMessages());
            var_dump($errors);
            die('not valid');
        }
        die('valid');
        $user = new Application_Model_User();
        $userModel = $user->getUserByEmail($userEmail);

        //-- check if user mail exists
        if ($userModel) {
            //-- generate mail link

            $forgotPasswordModel = new Application_Model_ForgotPassword();
            ;
            //-- store request
            if ($forgotPasswordModel->saveRequest($userModel['id'])) {
                $forgotPasswordModel->setLinkHashId($userModel);

                $link = '/index_password/resetpassword/id/' . $forgotPasswordModel->getGeneratedHashId();

                //-- send email
                $mailer = new Application_Model_Mail($this->view);
                $mailer->sendForgotPasswordMail($userModel, $languagesMetaData, $link);
            }
        }

        $this->_redirect('/index_password');
    }

    public function resetpasswordAction()
    {
        $userHash = base64_decode($this->getRequest()->getParam('id'));

        $paramArray = $this->getParamsFromHash($userHash);

        $forgotPasswordModel = new Application_Model_ForgotPassword();

        if ($forgotPasswordModel->isValidRequest($paramArray['id'], $paramArray['userid'])) {
            echo "is valid";
        } else {
            #$this->view->errors['linkNotValid']                = "Link nicht valide";
            #$this->_redirect('/index_password');
        }

        exit;
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
}

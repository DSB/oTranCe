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
class RegisterController extends OtranceController
{
    /**
     * @var Application_Model_Languages
     */
    protected $_languagesModel;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_languagesModel = new Application_Model_Languages();
    }

    /**
     * Register a user
     *
     * @return void
     */
    public function indexAction()
    {
        $default           = array(
            'id'          => 0,
            'active'      => 0,
            'username'    => '',
            'pass1'       => '',
            'pass2'       => '',
            'newLanguage' => '',
            'email'       => '',
            'realName'    => '',
        );
        $validationErrors  = array();
        $userData          = $this->_request->getParam('user', $default);
        $languagesMetaData = $this->_languagesModel->getAllLanguages();
        $translator        = Msd_Language::getInstance();
        if ($this->_request->isPost() && $this->_request->getParam('switchLanguage', null) === null) {
            $userModel          = new Application_Model_User();
            $userData['id']     = 0;
            $userData['active'] = 0;

            $languageSelected = true;
            if (empty($userData['lang']) && $userData['newLanguage'] == '') {
                $languageSelected                      = false;
                $validationErrors['selectLanguage'][0] = $translator->translate('L_ERROR_SELECT_LANGUAGE');
            }

            if ($userModel->validateData($userData, $translator)) {
                if ($languageSelected !== false) {
                    $this->view->registerSuccess = false;
                    $userId                      = $userModel->registerUser($userData, $languagesMetaData);
                    if ($userId !== false) {
                        $this->view->registerSuccess = true;
                        // send e-mail to administrator
                        $mailer = new Application_Model_Mail($this->view);
                        $mailer->sendUserRegisteredMail($userData, $languagesMetaData);
                    }
                }
            } else {
                $validationErrors = array_merge($validationErrors, $userModel->getValidateMessages());
            }
        }
        $this->view->errors                = $validationErrors;
        $this->view->isLogin               = true;
        $this->view->request               = $this->_request;
        $this->view->user                  = $userData;
        $this->view->availableGuiLanguages = $this->view->dynamicConfig->getParam('availableGuiLanguages');
        $this->view->editLanguages         = $languagesMetaData;
    }

}

<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Index controller
 *
 * Controller to handle actions triggered on index screen
 *
 * @package         MySQLDumper
 * @subpackage      Controllers
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * Remember last controler
     * @var string
     */
    private $_lastController;

    /**
     * Remember last action
     * @var string
     */
    private $_lastAction;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_lastController = $this->_request->getParam('lastController', 'index');
        $this->_lastAction = $this->_request->getParam('lastAction', 'index');
    }

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $languagesModel = new Application_Model_Languages();
        $entriesModel = new Application_Model_LanguageEntries();
        $userModel = new Application_Model_User();
        $this->view->languages = $languagesModel->getAllLanguages();
        $this->view->translators = $userModel->getTranslators();
        $this->view->status = $entriesModel->getStatus($this->view->languages);
    }

    /**
     * Redirect to url
     *
     * @param array $url
     *
     * @return void
     */
    private function _doRedirect(array $url = array())
    {
        $this->_response->setRedirect($this->view->url($url, null, true));
    }

    /**
     * Logout the user and redirect him to login page
     */
    public function logoutAction()
    {
        $historyModel = new Application_Model_History();
        $historyModel->logLogout();
        //un-Auth user
        $user = new Msd_User();
        $user->logout();
        setcookie('oTranCe_autologin', null, null, '/');
        $this->_doRedirect(
            array(
                'controller' => 'index',
                'action' => 'login',
            )
        );
    }

    /**
     * User login
     *
     * @return void
     */
    public function loginAction()
    {
        // Set view parameter for layout to say "Hey it's the login page".
        $this->view->isLogin = true;
        $form = new Application_Form_Login();
        $loginResult = false;
        if ($this->_request->isPost()) {
            $historyModel = new Application_Model_History();
            $user = new Msd_User();
            $postData = $this->_request->getParams();
            if ($form->isValid($postData)) {
                $autoLogin = ($postData['autologin'] == 1) ? true : false;
                $loginResult = $user->login(
                    $postData['user'],
                    $postData['pass'],
                    $autoLogin
                );
                $this->view->messages = $user->getAuthMessages();
                if ($loginResult === Msd_User::SUCCESS) {
                        $historyModel->logLoginSuccess();
                        $this->_doRedirect(
                            array(
                                 'controller' => 'index',
                                 'action' => 'index',
                            )
                        );
                } else {
                    $loginResult = false;
                }
            }

            if ($loginResult === false) {
                $historyModel->logLoginFailed($postData['user']);
                $this->view->popUpMessage()->addMessage(
                    'login-message',
                    'L_LOGIN',
                    'L_LOGIN_INVALID_USER',
                    array(
                        'modal' => true,
                        'dialogClass' => 'error'
                    )
                );
            }
        }
        $this->view->form = $form;
    }

}

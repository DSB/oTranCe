<?php
require_once('AdminController.php');
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Users Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 */
class Admin_UsersController extends AdminController
{
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        if (!$this->_userModel->hasRight('editUsers')) {
            $this->_redirect('/');
        }
    }

    /**
     * Index action for maintaining users
     *
     * @return void
     */
    public function indexAction()
    {
        $deleteUser = $this->_request->getParam('deleteUser', 0);
        if ($deleteUser > 0) {
            $this->_forward('delete-user');
        }
        if ($this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage', null) == null) {
            $this->_setSessionParams();
        }

        $recordsPerPage = (int) $this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->users = $this->_userModel->getUsers(
            (string) $this->_dynamicConfig->getParam($this->_requestedController . '.filterUser'),
            (int) $this->_dynamicConfig->getParam($this->_requestedController . '.offset'),
            $recordsPerPage
        );
        $this->view->hits = $this->_userModel->getRowCount();
        $this->view->userModel = $this->_userModel;
    }

    /**
     * Edit action
     *
     * @return void
     */
    public function editAction()
    {
        $userId = (int) $this->_request->getParam('id', 0);
        if ($userId == 0) {
            // Is current user allowed to add a new user?
            if (!$this->_userModel->hasRight('addUser')) {
                $this->_redirect('/');
            }

            //set default form values for new user
            $userData = array(
                'id'       => 0,
                'username' => '',
                'realName' => '',
                'email'    => '',
                'pass1'    => '',
                'pass2'    => '',
                'active'   => 0
            );
        } else {
            // get user data from database
            $userData = $this->_userModel->getUserById($userId);
        }

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $userData = array(
                'id'       => $params['id'],
                'username' => $params['username'],
                'realName' => $params['realName'],
                'email'    => $params['email'],
                'active'   => $params['active']
            );
            if (isset($params['saveAccount'])) {
                if ($userData['id'] == 0 || $params['pass1'] > '' || $params['pass2'] > '') {
                    $userData['pass1'] = $params['pass1'];
                    $userData['pass2'] = $params['pass2'];
                }
                $translator = Msd_Language::getInstance()->getTranslator();
                if ($this->_userModel->validateData($userData, $translator)) {
                    $result = $this->_saveAccountSettings($userData);
                    if ($result !== false) {
                        $userId = (int) $result;
                    }
                    $this->view->saved = (bool) $result;
                    $userData = $this->_userModel->getUserById($userId);
                } else {
                    $this->view->errors = $this->_userModel->getValidateMessages();
                }
            }
        }

        $this->view->userData = $userData;
        if ($userData['id'] == 0) {
            $this->view->userRights = $this->_userModel->getDefaultRights();
        } else {
            $this->view->userRights = $this->_userModel->getUserGlobalRights($userId);
        }
        $this->view->editLanguages = $this->_userModel->getUserLanguageRights($userId, false);
    }

    /**
     * Delete a user and all of his log entries
     *
     * @return void
     */
    public function deleteUserAction()
    {
        if (!$this->_userModel->hasRight('deleteUsers')) {
            $this->_redirect('/admin_users');
        }
        $userId = (int) $this->_request->getParam('deleteUser', 0);
        $historyModel = new Application_Model_History();
        $deleteResult = true;
        $deleteResult &= $historyModel->deleteEntriesByUserId($userId);
        $deleteResult &= $this->_userModel->deleteUserById($userId);
        if ($deleteResult == true) {
            $this->view->userDeleted = true;
            //trigger ajax call to optimize database tables
            $this->_dynamicConfig->setParam('optimizeTables', true);
        } else {
            $this->view->userDeleted = false;
        }
        // prevent endless forward-loop
        $this->_request->setParam('deleteUser', 0);
        $this->_forward('index');
    }

    /**
     * Save account settings to database
     *
     * @param array $userData Array containing username, pass1, active and id
     *
     * @return bool|int Return user id on succes or false on error
     */
    public function _saveAccountSettings($userData)
    {
        return $this->_userModel->saveAccount($userData);
    }

}

<?php
require_once('AdminController.php');
class Admin_UsersController extends AdminController
{
    /**
     * Index action for maintaining users
     *
     * @return void
     */
    public function indexAction()
    {
        $recordsPerPage = (int) $this->_config->get('dynamic.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->users = $this->_userModel->getUsers(
            $this->_config->get('dynamic.filterUser'),
            $this->_config->get('dynamic.offset'),
            $this->_config->get('dynamic.recordsPerPage')
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
        $userId = $this->_request->getParam('id', 0);
        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            if (isset($params['saveAccount'])) {
                if ($this->_validateAccountSettings()) {
                    $newUserData = $this->_saveAccountSettings();
                    if ($newUserData !== false) {
                        $userId = $newUserData['id'];
                        $this->view->saveMessage = true;
                    };
                }
            }
        }
        if ($userId == 0) {
            // create new user - set defaults
            $user = array(
                'id' => 0,
                'username' => '',
                'active' => 1
            );
        } else {
            $user = $this->_userModel->getUserById($userId);
        }
        $this->view->user = $user;
    }

    /**
     * Validate inputs for account settings
     *
     * @return bool
     */
    public function _validateAccountSettings()
    {
        $errors = array();
        $params = $this->_request->getParams();
        $strLenValidate = new Zend_Validate_StringLength(array('min' => 2, 'max' => 50));
        if (!$strLenValidate->isValid($params['user_name'])) {
            $errors = array_merge($errors, $strLenValidate->getMessages());
        }
        if ($params['pass1'] > '' || $params['pass2'] > '') {
            if ($params['pass1'] != $params['pass2']) {
                $errors[] = 'The passwords are not the same.';
            }
        }

        if ($params['id'] == 0 && $params['pass1'] == '') {
            $errors[] = 'You must provide a password when creating a new user account.';
        }
        $this->view->errors = $errors;
        if (empty($errors)) {
            return true;
        }
        return false;
    }

    /**
     * Save account settings to database
     *
     * @return void
     */
    public function _saveAccountSettings()
    {
        $params = $this->_request->getParams();
        return $this->_userModel->saveAccount($params);
    }
}
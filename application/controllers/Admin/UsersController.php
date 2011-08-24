<?php
require_once('AdminController.php');
/**
 * Controller for user administration.
 */
class Admin_UsersController extends AdminController
{
    /**
     * Index action for maintaining users
     *
     * @return void
     */
    public function indexAction()
    {
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
        $userId = $this->_request->getParam('id', 0);
        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            if (isset($params['saveAccount'])) {
                if ($this->_validateAccountSettings()) {
                    $newUserData = $this->_saveAccountSettings();
                    if ($newUserData !== false) {
                        $userId = $newUserData['id'];
                        $res = $this->_saveUserRights($params);
                        $res &= $this->_saveLanguageEditRights($params);
                        if ($res == true) {
                            $this->view->saveMessage = true;
                        } else {
                            $this->view->saveErrorMessage = true;
                        }
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
        $this->view->userRights = $this->_userModel->getUserGlobalRights($userId);
        $this->view->editLanguages = $this->_userModel->getUserRights('edit', $userId);
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
            $errors['user_name'] = $strLenValidate->getMessages();
        }
        if ($params['pass1'] > '' || $params['pass2'] > '') {
            $identicalValidate = new Zend_Validate_Identical($params['pass1']);
            if (!$identicalValidate->isValid($params['pass2'])) {
                $errors['pass1'] = $identicalValidate->getMessages();
            }
        }

        if ($params['id'] == 0) {
            $notEmptyValidate = new Zend_Validate_NotEmpty();
            if (!$notEmptyValidate->isValid($params['pass1'])) {
                $errors['pass1'] = $notEmptyValidate->getMessages();
            }
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
     * @return bool|id Return user id on succes or false on error
     */
    public function _saveAccountSettings()
    {
        $params = $this->_request->getParams();
        return $this->_userModel->saveAccount($params);
    }

    /**
     * Save general user rights
     *
     * @param array $params Post-parameters
     *
     * @return bool
     */
    public function _saveUserRights($params)
    {
        $res = true;
        $rights = array('addVar', 'admin', 'export', 'createFile');
        foreach ($rights as $right) {
            if (isset($params[$right]) && $params[$right] == 1) {
                $res &= $this->_userModel->saveRight($params['id'], $right, 1);
            } else {
                $res &= $this->_userModel->deleteRight($params['id'], $right, 1);
            }
        }
        return $res;
    }

    /**
     * Save language edit rights of user
     *
     * @param array $params Post-parameters
     *
     * @return bool
     */
    public function _saveLanguageEditRights($params)
    {
        $res = true;
        $languages = array();
        $keys = array_keys($params);
        // extract lang edit rights
        foreach ($keys as $key) {
            if (substr($key, 0, 5) == 'lang-') {
                $languages[] = substr($key, 5);
            }
        }
        // first remove all other languages
        $this->_userModel->deleteLanguageRights($params['id'], $languages);
        // set language rights
        foreach ($languages as $language) {
            if ($this->_userModel->getRight($params['id'], 'edit', $language) == false) {
                $res &= $this->_userModel->saveRight($params['id'], 'edit', $language);
            } else {
            }
        }
        return $res;
    }
}

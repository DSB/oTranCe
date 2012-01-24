<?php
require_once('AdminController.php');
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Users Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
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
        $recordsPerPage = (int) $this->_dynamicConfig->getParam(
            $this->_requestedController . '.recordsPerPage',
            $this->_dynamicConfig->getParam('recordsPerPage', 10)
        );

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
            if (!$this->_userModel->hasRight('addUser')) {
                $this->_redirect('/');
            }
        }
        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            if (isset($params['saveAccount'])) {
                if ($this->_validateAccountSettings()) {
                    $newUserData = $this->_saveAccountSettings();
                    if ($newUserData !== false) {
                        $userId = (int) $newUserData;
                        $params['id'] = $userId;
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

        $userDefaults = $this->_userModel->getDefaultRights();
        $userGlobalDefaults = array(
            'editConfig' => 1,
        );
        $this->view->user          = array_merge($userDefaults, $this->_userModel->getUserById($userId));
        $this->view->userRights    = array_merge($userGlobalDefaults, $this->_userModel->getUserGlobalRights($userId));
        $this->view->editLanguages = $this->_userModel->getUserLanguageRights($userId, false);
    }

    /**
     * Validate inputs for account settings and set view error mesages.
     *
     * @return bool
     */
    public function _validateAccountSettings()
    {
        $errors = array();
        $params = $this->_request->getParams();

        if ($params['id'] == 0) {
            $notEmptyValidate = new Zend_Validate_NotEmpty();
            if (!$notEmptyValidate->isValid($params['pass1'])) {
                $errors['pass1'] = $notEmptyValidate->getMessages();
            }

            // check if we already have a user with that name
            $existingUser = $this->_userModel->getUserByName($params['user_name']);
            if (!empty($existingUser)) {
                $errors['user_name'] = array();
                $errors['user_name'][] = 'A user with the name \'' . $params['user_name'] .'\' already exists!';
            }
        }

        $strLenValidate = new Zend_Validate_StringLength(array('min' => 2, 'max' => 50));
        if (!$strLenValidate->isValid($params['user_name'])) {
            if (!isset($errors['user_name']) || !is_array($errors['user_name'])) {
                $errors['user_name'] = array();
            }
            $errors['user_name'] = $strLenValidate->getMessages();
        }

        if ($params['pass1'] > '' || $params['pass2'] > '') {
            $identicalValidate = new Zend_Validate_Identical($params['pass1']);
            if (!$identicalValidate->isValid($params['pass2'])) {
                $errors['pass1'] = $identicalValidate->getMessages();
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
        $rights = $this->_userModel->getDefaultRights();
        foreach ($rights as $right => $defaultValue) {
            if (isset($params[$right])) {
                $res &= $this->_userModel->saveRight($params['id'], $right, $params[$right]);
            } else {
                $res &= $this->_userModel->saveRight($params['id'], $right, $defaultValue);
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
        $languages = array();
        $keys = array_keys($params);
        // extract lang edit rights
        foreach ($keys as $key) {
            if (substr($key, 0, 5) == 'lang-') {
                $languages[] = substr($key, 5);
            }
        }
        $res = $this->_userModel->saveLanguageRights($params['id'], $languages);
        return $res;
    }

}

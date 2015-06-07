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
     * Check general access right
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        $this->checkRight('editUsers');
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

        $recordsPerPage                =
            (int)$this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);

        $sortField     = $this->getParam('sortfield', 'username');
        $sortField     = $this->getValidatedSortField($sortField);
        $sortDirection = (int)$this->getParam('direction', SORT_ASC);
        $this->view->assign('sortDirection', $sortDirection);

        $statisticsModel = new Application_Model_Statistics();
        $statistics      = $statisticsModel->getUserOverallStatistics(
            (string)$this->_dynamicConfig->getParam($this->_requestedController . '.filterUser'),
            (int)$this->_dynamicConfig->getParam($this->_requestedController . '.offset'),
            $recordsPerPage,
            $sortField,
            $sortDirection
        );

        $this->view->hits      = $statisticsModel->getRowCount();
        $this->view->userModel = $this->_userModel;
        $this->view->users     = $statistics;
    }

    /**
     * Edit action
     *
     * @return void
     */
    public function editAction()
    {
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

        $userId = (int)$this->_request->getParam('id', 0);
        if ($userId == 0) {
            // Is current user allowed to add a new user?
            //if (!$this->checkRight('addUser')) {
            //    return;
            //}
        } else {
            // get user data from database
            $userData = $this->_userModel->getUserById($userId);
        }

        if ($this->_request->isPost()) {
            $oldUserStatus = $userData['active'];
            $params        = $this->_request->getParams();
            $userData      = array(
                'id'       => (int)$params['id'],
                'username' => $params['username'],
                'realName' => $params['realName'],
                'email'    => $params['email'],
                'active'   => $params['active']
            );

            if (isset($params['saveAccount'])) {
                // if password changed or new user added we need to add and validate the password
                if ($userData['id'] == 0 || $params['pass1'] > '' || $params['pass2'] > '') {
                    $userData['pass1'] = $params['pass1'];
                    $userData['pass2'] = $params['pass2'];
                }
                $translator = Msd_Language::getInstance();
                if ($this->_userModel->validateData($userData, $translator)) {
                    $this->view->saved = (bool)$this->_saveAccountSettings($userData, $oldUserStatus);
                    $userData          = $this->_userModel->getUserById($userId);
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
        if (!$this->checkRight('deleteUsers')) {
            return;
        }

        $userId       = (int)$this->_request->getParam('deleteUser', 0);
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
        $this->forward('index');
    }

    /**
     * Save account settings to database
     *
     * @param array $userData      Array containing username, pass1, active and id
     * @param bool  $oldUserStatus Bool to reflect whether the user was active/inactive before saving
     *
     * @return bool|int Return user id on success or false on error
     */
    public function _saveAccountSettings($userData, $oldUserStatus)
    {
        $result = $this->_userModel->saveAccount($userData);
        if ($result !== false) {
            $userId = (int)$result;
            if ($userData['id'] == 0) {
                $this->_userModel->addFallBackLanguageAsReferenceLanguage($userId);
            }

            // if user status changed -> log it
            if ($oldUserStatus != $userData['active']) {
                $historyModel = new Application_Model_History();
                if ($userData['active'] == 1) {
                    // inform user via e-mail that his account has been activated
                    $mailer = new Application_Model_Mail($this->view);
                    $mailer->sendAccountActivationInfoMail($userData);
                    $historyModel->logUserAccountApproved($userId);
                } else {
                    // acount was switched to inactive
                    // TODO Decide if we also want to inform the user if his account was closed
                    $historyModel->logUserAccountClosed($userId);
                }
            }
        }

        return $result;
    }

}

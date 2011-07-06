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
        $params = $this->_request->getParams();
        if (isset($params['edit'])) {
            echo "Jo edit: " . $params['edit'];
        }

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

    public function editAction()
    {

    }
}
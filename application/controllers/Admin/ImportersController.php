<?php
require_once 'AdminController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Vcs Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 */
class Admin_ImportersController extends AdminController
{
    /**
     * @var \Application_Model_Importers
     */
    protected $_importerModel;


    /**
     * Check general access right
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        $this->checkRight('editImporter');
    }

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->_importerModel = new Application_Model_Importers();
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->importers = $this->_importerModel->getImporter();
    }
}

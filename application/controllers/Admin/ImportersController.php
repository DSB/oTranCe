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
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        if (!$this->_userModel->hasRight('editImporter')) {
            $this->_redirect('/error/not-allowed');
        }
        $this->_importerModel = new Application_Model_Importers();
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $importers = $this->_importerModel->getImporter();
        $this->view->importers = $importers;
    }
}

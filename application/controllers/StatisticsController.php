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
 * Statistics Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class StatisticsController extends Zend_Controller_Action
{

    /**
     * Statitics model
     * @var Application_Model_Statistics
     */
    private $_statisticsModel;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $userModel = new Application_Model_User();
        if (!$userModel->hasRight('showStatistics')) {
            $this->_redirect('/');
        }

        $this->_statisticsModel = new Application_Model_Statistics();
    }
    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->userStatistics = $this->_statisticsModel->getUserstatistics();
        $languagesModel = new Application_Model_Languages();
        $this->view->languages = $languagesModel->getAllLanguages();
    }

}

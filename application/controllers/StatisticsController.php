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
 * Statistics controller
 *
 * Controller to handle actions triggered on index screen
 *
 * @package         MySQLDumper
 * @subpackage      Controllers
 */
class StatisticsController extends Zend_Controller_Action
{

    /**
     * Statitics model
     * @var Application_Model_Statistics
     */
    private $_statisticsModel;

    public function init()
    {
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
        $languagesModel = new Application_Model_LanguageEntries();
        $this->view->languages = $languagesModel->getLanguages();
    }

}

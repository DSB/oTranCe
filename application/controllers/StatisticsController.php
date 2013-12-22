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
class StatisticsController extends OtranceController
{
    /**
     * Statistics model
     *
     * @var Application_Model_Statistics
     */
    private $_statisticsModel;

    /**
     * Check general access right
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        $this->checkRight('showStatistics');
    }

    /**
     * Init
     *
     * @return void
     */
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
        $languagesModel = new Application_Model_Languages();
        $languages      = $languagesModel->getAllLanguages();
        $statistics     = $this->_statisticsModel->getUserstatistics();
        $statistics     = $this->_addTotalEditActions($statistics, $this->_statisticsModel->getUserChangeStatistics());
        $statistics     = $this->_sortStatistics($statistics);

        $this->view->userStatistics = $statistics;
        $this->view->languages      = $languages;
        $this->view->user           = $this->_userModel;
    }

    /**
     * Enrich statistics array with total edit action nad last activity info.
     *
     * @param array $statistics The statistic array without assoc index editsTotal
     * @param array $totalStats Array holding total edit info
     *
     * @return array            New array having assoc index editsTotal
     */
    private function _addTotalEditActions($statistics, $totalStats)
    {
        foreach ($statistics as $index => $values) {
            $userId                                 = $values['user_id'];
            $statistics[$index]['editActionsTotal'] = $totalStats[$userId]['editActions'];
            $statistics[$index]['lastAction']       = $totalStats[$userId]['lastAction'];
        }

        return $statistics;
    }

    /**
     * Sort languages status for output
     *
     * @param array $statistics Languages status array
     *
     * @return array Sorted languages array
     */
    private function _sortStatistics($statistics)
    {
        $sortField     = $this->getParam('sortfield', 'username');
        $sortDirection = (int)$this->getParam('direction', SORT_ASC);
        $sort          = array($sortField => $sortDirection);
        $this->view->assign('sortDirection', $sortDirection);

        // sort by second criteria locale for equal value groups if first sorting isn't already set to locale
        if ($sortField !== 'locale') {
            $sort['locale'] = SORT_ASC;
        } else {
            $sort['username'] = SORT_ASC;
        }

        $statistics = Msd_ArraySort::sortMultidimensionalArray($statistics, $sort);

        return $statistics;
    }

}

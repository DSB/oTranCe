<?php
/**
 * @group Statistics
 * @group Models
 */
class StatisticsTest extends ControllerTestCase
{
    public function testGetUserstatistics()
    {
        /**
         * @var Application_Model_Statistics
         */
        $this->statisticsModel = new Application_Model_Statistics();
        $statistics = $this->statisticsModel->getUserstatistics();
        //@TODO create fixture with statistic data for test
        $this->assertEquals(array(), $statistics);
    }

}
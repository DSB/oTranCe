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
        $expected = array(
            0 => array(
                    'user_id'     => '1',
                    'lang_id'     => '1',
                    'editActions' => '1',
                    'username'    => 'Admin'
                ),
            1 => array(
                    'user_id'     => '1',
                    'lang_id'     => '2',
                    'editActions' => '1',
                    'username'    => 'Admin'
                )
        );
        $this->assertEquals($expected, $statistics);
    }

}

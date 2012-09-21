<?php
/**
 * @group Statistics
 * @group Models
 */
class StatisticsTest extends ControllerTestCase
{
    /**
     * @var \Application_Model_Statistics
     */
    private $statisticsModel;

    public function setUp()
    {
        $this->statisticsModel = new Application_Model_Statistics();
    }

    public function testGetUserstatistics()
    {
        $statistics = $this->statisticsModel->getUserstatistics();
        $expected   = array(
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

    public function testCanGetUserChangeStatistics()
    {
        $res      = $this->statisticsModel->getUserChangeStatistics();
        $expected = array(
            1 =>
            array(
                'user_id'     => '1',
                'editActions' => '2',
                'lastAction'  => '2012-03-03 20:39:16',
            ),
        );
        $this->assertEquals($expected, $res);
    }
}

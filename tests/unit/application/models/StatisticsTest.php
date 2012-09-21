<?php
/**
 * @group Statistics
 * @group Models
 */
class StatisticsTest extends PHPUnit_Framework_TestCase
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
                'editActions' => '758',
                'username'    => 'Admin'
            ),
            1 => array(
                'user_id'     => '1',
                'lang_id'     => '2',
                'editActions' => '755',
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
                'editActions' => '1513',
            ),
        );
        unset($res[1]['lastAction']); // skip timestamp
        $this->assertEquals($expected, $res);
    }
}

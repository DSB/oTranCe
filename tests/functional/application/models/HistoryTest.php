<?php
/**
 * @group History
 * @group Models
 */
class HistoryTest extends ControllerTestCase
{
    /**
     * @var \Application_Model_History
     */
    protected $model;

    public function setUp()
    {
        $this->model = new Application_Model_History();
    }

    public function testGetEntries()
    {
        $entries = $this->model->getEntries();

        $expected = array(
            'id'       => 1,
            'user_id'  => 1,
            'dt'       => '2012-03-03 16:33:05',
            'key_id'   => null,
            'action'   => 'logged out',
            'lang_id'  => 0,
            'oldValue' => '-',
            'newValue' => '-',
            'key'      => null
        );

        $this->assertEquals(15, sizeof($entries));
        $this->assertEquals($expected, $entries[14]);

        // test user filter
        $entries = $this->model->getEntries(0, 2, 0, 1);
        $this->assertEquals(2, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertEquals(1, $entry['user_id']);
        }

        // test language filter
        $entries = $this->model->getEntries(0, 50, 1, 0);
        $this->assertEquals(1, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertEquals(1, $entry['lang_id']);
        }

        // test action filter
        $entries = $this->model->getEntries(0, 50, 0, 0, 'logged in');
        $this->assertEquals(6, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertEquals('logged in', $entry['action']);
        }
    }


}
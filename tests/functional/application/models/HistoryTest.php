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

        $entries = $this->model->getEntries(0, 50, 0, 0, '%logged%');
        $this->assertEquals(12, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertTrue(strpos($entry['action'], 'logged') !== false);
        }
    }

    public function testLogChanges()
    {
        // make sure actions are done with the admin user
        $this->loginUser('Admin', 'admin');
        $this->userModel = new Application_Model_User();

        $oldValues = array(
            1 => 'Test eintrag',
            2 => 'Test record'
        );
        $newValues = array(
            1 => 'Testeintrag',
            2 => 'Test Record'
        );
        $this->model->logChanges(1, $oldValues, $newValues);

        $entries = $this->model->getEntries(0, 50, 0, 1, 'changed');

        $this->assertTrue($entries[0]['oldValue'] == 'Test eintrag');
        $this->assertTrue($entries[0]['newValue'] == 'Testeintrag');

        $this->assertTrue($entries[1]['oldValue'] == 'Test record');
        $this->assertTrue($entries[1]['newValue'] == 'Test Record');

        // test delete. Since the model doesn't have a method to get the ids we do it here.
        $deleted = $this->model->deleteById($entries[0]['id']);
        $this->assertTrue($deleted);

        $deleted = $this->model->deleteById($entries[1]['id']);
        $this->assertTrue($deleted);

        $entries = $this->model->getEntries(0, 50, 0, 1, 'changed');
        foreach ($entries as $entry) {
            $this->assertTrue($entry['newValue'] !== 'Testeintrag');
            $this->assertTrue($entry['newValue'] !== 'Test Record');
        }

    }

}
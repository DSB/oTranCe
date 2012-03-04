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

    public static function setUpBeforeClass()
    {
        Testhelper::setUpDb('history.sql');
    }

    public function setUp()
    {
        // make sure actions in this test are done with the admin user
        $this->loginUser('Admin', 'admin');
        $this->userModel = new Application_Model_User();

        $this->model = new Application_Model_History();
    }

    public function testGetEntries()
    {
        $entries = $this->model->getEntries(0, 50, 0, 0, 'created');
        $this->assertTrue(!empty($entries));

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
        $this->assertEquals(5, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertEquals('logged in', $entry['action']);
        }

        $entries = $this->model->getEntries(0, 50, 0, 0, '%logged%');
        $this->assertEquals(7, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertTrue(strpos($entry['action'], 'logged') !== false);
        }

        // check row count
        $rowsFound = $this->model->getRowCount();
        $this->assertEquals(7, $rowsFound);


    }

    public function testLogChanges()
    {
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

        // check automatic setting of old value if it isn't present
        $this->model->logChanges(1, array(), array(1 => 'Testeintrag2'));
        $entries = $this->model->getEntries(0, 50, 0, 1, 'changed');
        $this->assertTrue($entries[0]['oldValue'] == '-');
        $this->assertTrue($entries[0]['newValue'] == 'Testeintrag2');

        $deleted = $this->model->deleteById($entries[0]['id']);
        $this->assertTrue($deleted);
    }

    /**
     * @depends testLogChanges
     */
    public function testGetLatestChange()
    {
        $latestChange = $this->model->getLatestChange(1);
        $this->assertEquals('2012-03-03 20:39:16', $latestChange);

        $latestChange = $this->model->getLatestChange(2);
        $this->assertEquals('2012-03-03 20:40:02', $latestChange);
    }
}
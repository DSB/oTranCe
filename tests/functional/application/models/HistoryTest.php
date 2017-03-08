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
    protected $historyModel;

    /**
     * @var \Application_Model_LanguageEntries
     */
    protected $entriesModel;

    public static function setUpBeforeClass()
    {
        Testhelper::setUpDb('History.sql');
        Testhelper::setUpDb('db_schema_update8.sql');
    }

    public function setUp()
    {
        // make sure actions in this test are done with the admin user
        $this->loginUser('Admin', 'admin');
        $this->entriesModel = new Application_Model_LanguageEntries();
        $this->historyModel = new Application_Model_History();
    }

    public function testGetEntries()
    {
        $entries = $this->historyModel->getEntries(0, 50, 0, 0, 'created');
        $this->assertTrue(!empty($entries));

        // test user filter
        $entries = $this->historyModel->getEntries(0, 2, 0, 1);
        $this->assertEquals(2, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertEquals(1, $entry['user_id']);
        }

        // test language filter
        $entries = $this->historyModel->getEntries(0, 50, 1, 0);
        $this->assertEquals(1, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertEquals(1, $entry['lang_id']);
        }

        // test action filter
        $entries = $this->historyModel->getEntries(0, 50, 0, 0, 'logged in');
        $this->assertEquals(5, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertEquals('logged in', $entry['action']);
        }

        $entries = $this->historyModel->getEntries(0, 50, 0, 0, '%logged%');
        $this->assertEquals(7, sizeof($entries));
        foreach ($entries as $entry) {
            $this->assertTrue(strpos($entry['action'], 'logged') !== false);
        }

        // check row count
        $rowsFound = $this->historyModel->getRowCount();
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
        $this->historyModel->logChanges(1, $oldValues, $newValues);

        $entries = $this->historyModel->getEntries(0, 50, 0, 1, 'changed');

        $this->assertTrue($entries[0]['oldValue'] == 'Test eintrag');
        $this->assertTrue($entries[0]['newValue'] == 'Testeintrag');

        $this->assertTrue($entries[1]['oldValue'] == 'Test record');
        $this->assertTrue($entries[1]['newValue'] == 'Test Record');

        // test delete. Since the model doesn't have a method to get the ids we do it here.
        $deleted = $this->historyModel->deleteById($entries[0]['id']);
        $this->assertTrue($deleted);

        $deleted = $this->historyModel->deleteById($entries[1]['id']);
        $this->assertTrue($deleted);

        $entries = $this->historyModel->getEntries(0, 50, 0, 1, 'changed');
        foreach ($entries as $entry) {
            $this->assertTrue($entry['newValue'] !== 'Testeintrag');
            $this->assertTrue($entry['newValue'] !== 'Test Record');
        }

        // check automatic setting of old value if it isn't present
        $this->historyModel->logChanges(1, array(), array(1 => 'Testeintrag2'));
        $entries = $this->historyModel->getEntries(0, 50, 0, 1, 'changed');
        $this->assertTrue($entries[0]['oldValue'] == '');
        $this->assertTrue($entries[0]['newValue'] == 'Testeintrag2');

        $deleted = $this->historyModel->deleteById($entries[0]['id']);
        $this->assertTrue($deleted);
    }

    /**
     * @depends testLogChanges
     */
    public function testGetLatestChange()
    {
        $latestChange = $this->historyModel->getLatestChange(1);
        $this->assertEquals('2012-03-03 20:39:16', $latestChange);

        $latestChange = $this->historyModel->getLatestChange(2);
        $this->assertEquals('2012-03-03 20:40:02', $latestChange);
    }

    public function testLogNewVarCreated()
    {
        $languageEntriesModel = new Application_Model_LanguageEntries();
        $languageEntriesModel->saveNewKey('L_MY_TEST_KEY', 1);
        $entry = $languageEntriesModel->getEntryByKey('L_MY_TEST_KEY', 1);
        $this->historyModel->logNewVarCreated($entry['id']);

        $entries = $this->historyModel->getEntries(0, 50, 0, 1, 'created');
        $this->assertTrue($entries[0]['key'] == 'L_MY_TEST_KEY');

        // clean up
        $this->historyModel->deleteById($entries[0]['id']);
        $languageEntriesModel->deleteEntryByKeyId($entries[0]['key_id']);
    }

    public function testLogVarDeleted()
    {
        $this->historyModel->logVarDeleted('TestDelete');
        $entries = $this->historyModel->getEntries(0, 50, 0, 1, 'deleted%');
        $this->assertTrue($entries[0]['action'] == 'deleted \'TestDelete\'');
        $this->historyModel->deleteById($entries[0]['id']);
    }

    public function testLogLogOut()
    {
        // force logging with actual timestamp
        $this->historyModel->logLogout();
        $entries = $this->historyModel->getEntries(0, 50, 0, 1, 'logged out');
        $this->assertTrue(sizeof($entries) > 0);
        // latest entry mustn't be older than now - 2 seconds
        $timestamp = Testhelper::mysql2timestamp($entries[0]['dt']);
        $this->assertTrue($timestamp > time() - 3);
        $this->historyModel->deleteById($entries[0]['id']);
    }

    public function testLogVcsUpdate()
    {
        // force logging with actual timestamp
        $this->historyModel->logVcsUpdate(99);
        $entries = $this->historyModel->getEntries(0, 50, 99, 0, 'updated VCS');
        $this->assertTrue(sizeof($entries) > 0);
        // latest entry mustn't be older than now - 2 seconds
        $timestamp = Testhelper::mysql2timestamp($entries[0]['dt']);
        $this->assertTrue($timestamp > time() - 2);
        $this->historyModel->deleteById($entries[0]['id']);
    }

    public function testLogVcsUpdateAll()
    {
        // force logging with actual timestamp
        $this->historyModel->logVcsUpdateAll();
        $entries = $this->historyModel->getEntries(0, 50, 0, 0, 'updated VCS');
        $this->assertTrue(sizeof($entries) > 0);
        // latest entry mustn't be older than now - 2 seconds
        $timestamp = Testhelper::mysql2timestamp($entries[0]['dt']);
        $this->assertTrue($timestamp > time() - 2);
        $this->assertTrue($entries[0]['lang_id'] == 0);
        $this->historyModel->deleteById($entries[0]['id']);
    }

    public function testDeleteEntriesByUserId()
    {
        // look for entries of test user
        $entries = $this->historyModel->getEntries(0, 50, 0, 2);
        // we should have 2 entries
        $this->assertEquals(2, sizeof($entries));

        // delete all entries of test user
        $this->historyModel->deleteEntriesByUserId(2);
        // again, get all entries of test user
        $entries = $this->historyModel->getEntries(0, 50, 0, 2);
        // now we must get an empty array
        $this->assertEquals(array(), $entries);
    }

    public function testLogVarNameChanged()
    {
        $this->historyModel->logVarNameChanged(1, 'L_TEST', 'L_NEW_TEST');
        $entries  = $this->historyModel->getEntries(0, 50, 0, 1, 'changed');
        $expected = array(
            'id'       => '35',
            'user_id'  => '1',
            'key_id'   => '1',
            'action'   => 'changed',
            'lang_id'  => '0',
            'oldValue' => 'L_TEST',
            'newValue' => 'L_NEW_TEST',
            'key'      => 'L_TEST',
        );
        unset($entries[0]['dt']); // skip timestamp
        $this->assertEquals($expected, $entries[0]);
        $this->historyModel->deleteById(35);
    }

    public function testCanLogRegistrationOfUser()
    {
        $entries  = $this->historyModel->getEntries(0, 100, 0, 2, 'registered');
        $expected = array();
        $this->assertEquals($expected, $entries);

        $this->historyModel->logUserRegistered(2);
        $entries = $this->historyModel->getEntries(0, 100, 0, 2, 'registered');
        $this->assertEquals(1, count($entries));
        $this->assertEquals('registered', $entries[0]['action']);
        $this->historyModel->deleteById($entries[0]['id']);
    }

    public function testCanLogUserAccountClosed()
    {
        $entries  = $this->historyModel->getEntries(0, 100, 0, 2, 'account closed by %');
        $expected = array();
        $this->assertEquals($expected, $entries);

        $this->historyModel->logUserAccountClosed(2);
        $entries = $this->historyModel->getEntries(0, 100, 0, 2, 'account closed by %');
        $this->assertEquals(1, count($entries));
        $this->assertEquals('account closed by Admin', $entries[0]['action']);
        $this->historyModel->deleteById($entries[0]['id']);
    }

    public function testCanLogUserAccountApproved()
    {
        $entries  = $this->historyModel->getEntries(0, 100, 0, 2, 'account approved by %');
        $expected = array();
        $this->assertEquals($expected, $entries);

        $this->historyModel->logUserAccountApproved(2);
        $entries = $this->historyModel->getEntries(0, 100, 0, 2, 'account approved by %');
        $this->assertEquals(1, count($entries));
        $this->assertEquals('account approved by Admin', $entries[0]['action']);
        $this->historyModel->deleteById($entries[0]['id']);
    }


}

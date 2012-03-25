<?php
/**
 * @group LanguageEntries
 * @group Models
 */
class LanguageEntriesTest extends ControllerTestCase
{
    /**
     * @var \Application_Model_LanguageEntries
     */
    protected $model;

    public static function setUpBeforeClass()
    {
        Testhelper::setUpDb('LanguageEntries.sql');
    }

    public function setUp()
    {
        // make sure actions in this test are done with the admin user
        $this->loginUser('Admin', 'admin');
        $this->model = new Application_Model_LanguageEntries();
    }

    public function testGetAllKeys()
    {
        $keys = $this->model->getAllKeys();
        $this->assertEquals(110, sizeof($keys));
    }


    public function testGetTranslations()
    {
        $translations = $this->model->getTranslations(1);
        $this->assertEquals(110, sizeof($translations));
        $this->assertEquals('Ersetze NULL durch', $translations[110]);

        $translations = $this->model->getTranslations(2);
        $this->assertEquals('Replace NULL with', $translations[110]);

        // positive false check with non existent language id
        $translations = $this->model->getTranslations(9999);
        $this->assertEquals(array(), $translations);

    }

    public function testGetStatus()
    {
        $languages = array(
            array('id' => 1),
            array('id' => 2)
        );

        $status = $this->model->getStatus($languages);
        // language de is at 97.27%
        $this->assertEquals(97.27, $status[1]['done']);
        // language en is at 100%
        $this->assertEquals(100, $status[2]['done']);
    }

    public function testsGetEntriesByKey()
    {
        $entries = $this->model->getEntriesByKey('L_CHECK');
        $expected = array(
            'id'          => 40,
            'key'         => 'L_CHECK',
            'template_id' => 1
        );
        $this->assertEquals($expected, $entries[0]);
    }

    public function testGetAssignedFileTemplate()
    {
        $template = $this->model->getAssignedFileTemplate(1);
        $expected = array(
            'id'       => 1,
            'name'     => 'Admin',
            'filename' => '{LOCALE}/lang.php'
        );
        $this->assertEquals($expected, $template);
    }

    public function testAssignFileTemplate()
    {
        // assign key 1 to template 99
        $saved = $this->model->assignFileTemplate(1, 99);
        $this->assertTrue($saved);

        // check
        $key = $this->model->getKeyById(1);
        $expected = array(
            'id'          => '1',
            'key'         => 'L_TEST',
            'template_id' => '99',
            'dt'          => '2012-03-03 20:39:02'
        );
        $this->assertEquals($expected, $key);

        // re-assign
        $saved = $this->model->assignFileTemplate(1, 1);
        $this->assertTrue($saved);
    }

    public function testUpdateKeyName()
    {
        $updated = $this->model->updateKeyName(1, 'L_TEST_XX');
        $this->assertTrue($updated);
        // check
        $key = $this->model->getKeyById(1);
        $expected = array(
            'id'          => '1',
            'key'         => 'L_TEST_XX',
            'template_id' => '1',
            'dt'          => '2012-03-03 20:39:02'
        );
        $this->assertEquals($expected, $key);
        // rollback
        $updated = $this->model->updateKeyName(1, 'L_TEST');
        $this->assertTrue($updated);
    }

    public function testGetEntryById()
    {
        $entry = $this->model->getEntryById(1, 2);
        $expected = array(2 => 'Test records');
        $this->assertEquals($expected, $entry);

        $entry = $this->model->getEntryById(1, 1);
        $expected = array(1 => 'Test eintrag');
        $this->assertEquals($expected, $entry);

        // get non existent
        $entry = $this->model->getEntryById(99999, 2);
        $expected = array();
        $this->assertEquals($expected, $entry);

        // check call with invalid params
        $entry = $this->model->getEntryById(0, 0);
        $expected = array();
        $this->assertEquals($expected, $entry);
    }

    public function testGetEntriesByKeys()
    {
        $keys = array('L_TEST');
        $entry = $this->model->getEntriesByKeys($keys, 1, 1);
        $expected = array('L_TEST' => 'Test eintrag');
        $this->assertEquals($expected, $entry);

        $entry = $this->model->getEntriesByKeys($keys, 1, 2);
        $expected = array('L_TEST' => 'Test records');
        $this->assertEquals($expected, $entry);

        // check non existent key returns empty string
        $entry = $this->model->getEntriesByKeys(array('IDontExist'), 1, 1);
        $expected = array('IDontExist' => '');
        $this->assertEquals($expected, $entry);
    }

    public function testGetEntryByKey()
    {
        $entry = $this->model->getEntryByKey('L_TEST', 1);
        $this->assertEquals(array('id' => 1), $entry);

        $entry = $this->model->getEntryByKey('IDontExist', 1);
        $this->assertFalse($entry);
    }

    public function testHasEntryWithKey()
    {
        $hasEntry = $this->model->hasEntryWithKey('L_TEST', 1);
        $this->assertTrue($hasEntry);

        $hasEntry = $this->model->hasEntryWithKey('IDontExist', 1);
        $this->assertFalse($hasEntry);
    }

    public function testGetEntriesByValue()
    {
        $entries = $this->model->getEntriesByValue(array(1), 'löschen', 0, 30);
        $expected = array(
            0 => array(
                    'id'          => 25,
                    'key'         => 'L_AUTODELETE',
                    'template_id' => 1
                ),
            1 => array(
                    'id'          => 57,
                    'key'         => 'L_CONFIG_AUTODELETE',
                    'template_id' => 1
                ),
        );
        $this->assertEquals($expected, $entries);

        // check pagination
        $entries = $this->model->getEntriesByValue(array(1), 'Soll', 10, 10);
        $this->assertEquals(6, sizeof($entries));
        $check = array(
            'L_CONFIRM_DELETE_FILE',
            'L_CONFIRM_DELETE_TABLES',
            'L_CONFIRM_DROP_DATABASES',
            'L_CONFIRM_RECIPIENT_DELETE',
            'L_CONFIRM_TRUNCATE_DATABASES',
            'L_CONFIRM_TRUNCATE_TABLES'
        );
        foreach ($entries as $entry) {
            $this->assertTrue(in_array($entry['key'], $check));
        }

        // check param $languages with empty array returns an empty array
        $entries = $this->model->getEntriesByValue(array(), 'löschen', 0, 30);
        $this->assertEquals(array(), $entries);

        // check for empty result set
        $entries = $this->model->getEntriesByValue(array(1), 'IDOnTExist', 0, 30);
        $this->assertEquals(array(), $entries);

        // check that param $nrOfRecords is set to 10 if it is lower (Result can be seen in CodeCoverage)
        $entries = $this->model->getEntriesByValue(array(1), 'IDOnTExist', 0, 1);
        $this->assertEquals(array(), $entries);
    }

    public function testGetEntriesByKey()
    {
        // check that we get 10 translations for template 1
        $entries = $this->model->getEntriesByKey('', 0, 10, 1);
        $this->assertEquals(10, sizeof($entries));

        // positive false check - check that we get 0 translations for template 2
        $entries = $this->model->getEntriesByKey('L_ADD', 0, 10, 2);
        $this->assertEquals(0, sizeof($entries));
    }

    public function testGetUntranslated()
    {
        // check we get 3 untranslated phrases in total
        $entries = $this->model->getUntranslated(1);
        $this->assertEquals(3, sizeof($entries));

        // check we find 1 untranslated key with "ACTION" in key name
        $entries = $this->model->getUntranslated(1, 'ACTION');
        $this->assertEquals(1, sizeof($entries));

        // check we find no untranslated key with "ACTION" in key name for template 2
        $entries = $this->model->getUntranslated(1, 'ACTION', 0, 5, 2);
        $this->assertEquals(0, sizeof($entries));
    }
}
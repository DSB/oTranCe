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
        $expected = array(
            1 => array(
                    'templateId' => 1,
                    'key'        => 'L_TEST'
                )
        );
        $this->assertEquals($expected, $keys);
    }


    public function testGetTranslations()
    {
        $translations = $this->model->getTranslations(1);
        $this->assertEquals(array(1 => 'Test eintrag'), $translations);

        $translations = $this->model->getTranslations(2);
        $this->assertEquals(array(1 => 'Test records'), $translations);

        // positive false check with non existent language id
        $translations = $this->model->getTranslations(99);
        $this->assertEquals(array(), $translations);

    }

    public function testGetStatus()
    {
        $languages = array(
            array('id' => 1),
            array('id' => 2)
        );

        $status = $this->model->getStatus($languages);
        foreach ($status as $stat) {
            $this->assertEquals(100, $stat['done']);
        };
    }

    public function testsGetEntriesByKey()
    {
        $entries = $this->model->getEntriesByKey('L_TE');
        $expected = array(
            'id'          => 1,
            'key'         => 'L_TEST',
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
        $entry = $this->model->getEntryById(99, 2);
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


}
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
}
<?php
/**
 * @group Languages
 * @group Models
 */
class LanguagesTest extends ControllerTestCase
{
    /**
     * @var \Application_Model_Languages
     */
    protected $model;

    public function setUp()
    {
        $this->model = new Application_Model_Languages();
    }

    public function testGetFallbackLanguage()
    {
        $fallbackLang = $this->model->getFallbackLanguage();
        $this->assertEquals(2, $fallbackLang);
    }

    public function testSetFallbackLanguage()
    {
        $this->model->setFallbackLanguage(1);
        $fallbackLang = $this->model->getFallbackLanguage();
        $this->assertEquals(1, $fallbackLang);
        $this->model->setFallbackLanguage(2);
    }

    public function testOptimizeAllTables()
    {
        $optimize = $this->model->optimizeAllTables();
        $this->assertTrue($optimize[0]['Table'] == 'phpunit_otc.languages');
        $this->assertTrue($optimize[0]['Op'] == 'optimize');
        $this->assertTrue(strtolower($optimize[0]['Msg_text']) == 'ok');
    }

    public function testGetLanguageIdFromLocale()
    {
        $languageId = $this->model->getLanguageIdFromLocale('de');
        $this->assertEquals(1, $languageId);

        $languageId = $this->model->getLanguageIdFromLocale('en');
        $this->assertEquals(2, $languageId);
    }

    public function testGetLanguageById()
    {
        $language = $this->model->getLanguageById(2);
        $expected = array(
            'id'             => 2,
            'active'         => 1,
            'locale'         => 'en',
            'name'           => 'English',
            'flag_extension' => 'gif'
        );
        $this->assertEquals($expected, $language);
    }

    public function testGetAllLanguages()
    {
        $languages = $this->model->getAllLanguages();
        $expected = array(
            1 => array(
                    'id' => 1,
                    'active' => 1,
                    'locale' => 'de',
                    'name' => 'Deutsch',
                    'flag_extension' => 'gif',
                    'hasFlag' => 1
                ),
            2 => array(
                    'id'             => 2,
                    'active'         => 1,
                    'locale'         => 'en',
                    'name'           => 'English',
                    'flag_extension' => 'gif',
                    'hasFlag'        => 1
                )
        );
        $this->assertEquals($expected, $languages);
        $rowsFound = $this->model->getRowCount();
        $this->assertEquals(2, $rowsFound);


        // check filter
        $languages = $this->model->getAllLanguages('Deutsch');
        $expected = array(
            1 => array(
                'id'             => 1,
                'active'         => 1,
                'locale'         => 'de',
                'name'           => 'Deutsch',
                'flag_extension' => 'gif',
                'hasFlag'        => 1
            )
        );
        $this->assertEquals($expected, $languages);

        // check combination of filter and active
        $languages = $this->model->getAllLanguages('Arabic', 0, 0, false);
        $expected = array(
            3 => array(
                'id'             => 3,
                'active'         => 0,
                'locale'         => 'ar',
                'name'           => 'Arabic',
                'flag_extension' => 'gif',
                'hasFlag'        => 1
            )
        );
        $this->assertEquals($expected, $languages);

        // check pagination
        $languages = $this->model->getAllLanguages('', 1, 1);
        $expected = array(
            2 => array(
                'id'             => 2,
                'active'         => 1,
                'locale'         => 'en',
                'name'           => 'English',
                'flag_extension' => 'gif',
                'hasFlag'        => 1
            )
        );
        $this->assertEquals($expected, $languages);
    }

    public function testLocaleExists()
    {
        $exists = $this->model->localeExists('de');
        $this->assertTrue($exists);

        $exists = $this->model->localeExists('IDontExist');
        $this->assertFalse($exists);
    }

    public function testDeleteFlag()
    {
        $this->model->deleteFlag(2);
        $language = $this->model->getLanguageById(2);
        $expected = array(
            'id'             => 2,
            'active'         => 1,
            'locale'         => 'en',
            'name'           => 'English',
            'flag_extension' => ''
        );
        $this->assertEquals($expected, $language);

        // restore language
        $saved = $this->model->saveLanguage(2, 1, 'en', 'English', 'gif');
        $this->assertTrue($saved);
    }

    public function testSaveLanguage()
    {
        $saved = $this->model->saveLanguage(0, 0, 'xx', 'Test-Lang', 'png');
        $this->assertTrue($saved);

        // negative check - try to save again
        $saved = $this->model->saveLanguage(0, 0, 'xx', 'Test-Lang', 'png');
        $expected = "The specified locale 'xx' already exists.";
        $this->assertEquals($expected, $saved);

        // delete language
        $languageId = $this->model->getLanguageIdFromLocale('xx');
        $this->model->deleteLanguage($languageId);
    }

    public function testsSaveLanguageStatus()
    {
        $saved = $this->model->saveLanguageStatus(3, 1);
        $this->assertTrue($saved);

        $language = $this->model->getLanguageById(3);
        $this->assertTrue($language['active'] == 1);

        $saved = $this->model->saveLanguageStatus(3, 0);
        $this->assertTrue($saved);

        $language = $this->model->getLanguageById(3);
        $this->assertTrue($language['active'] == 0);
    }
}
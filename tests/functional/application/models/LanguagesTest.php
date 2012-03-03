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

    public function testGetLanguageIdFromLocale()
    {
        $languageId = $this->model->getLanguageIdFromLocale('de');
        $this->assertEquals(1, $languageId);

        $languageId = $this->model->getLanguageIdFromLocale('en');
        $this->assertEquals(2, $languageId);
    }

    public function testOptimizeAllTables()
    {
        $optimize = $this->model->optimizeAllTables();
        $this->assertTrue($optimize[0]['Table'] == 'phpunit_otc.languages');
        $this->assertTrue($optimize[0]['Op'] == 'optimize');
        $this->assertTrue(strtolower($optimize[0]['Msg_text']) == 'ok');
    }
}
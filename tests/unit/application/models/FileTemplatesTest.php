<?php
/**
 * @group Models
 * @group FileTemplates
 */
class FileTemplatesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Application_Model_FileTemplates
     */
    protected $model;

    /**
     * @var array
     */
    protected $templates;

    /**
     * @var Msd_Language
     */
    protected $translator;

    /**
     * @var array
     */
    protected $tplData;

    public function setUp()
    {
        $this->model      = new Application_Model_FileTemplates();
        $this->templates  = $this->model->getFileTemplates();
        $this->translator = Msd_Language::getInstance();
        $this->tplData    = array(
            'id'       => '',
            'name'     => 'My template',
            'filename' => 'test.php',
            'header'   => '/* my template*/',
            'content'  => '{Key} => {VALUE}',
            'footer'   => '// my footer'
        );

    }

    public function testGetFileTemplates()
    {
        $this->assertTrue($this->templates[0]['id'] == 1);
        $this->assertTrue($this->templates[0]['name'] == 'Admin');
        $this->assertTrue($this->templates[0]['content'] == "'{KEY}' => '{VALUE}',");
        $this->assertTrue($this->templates[0]['filename'] == '{LOCALE}/lang.php');

        $this->assertTrue($this->templates[1]['id'] == 2);
        $this->assertTrue($this->templates[1]['name'] == 'Admin help');
        $this->assertTrue($this->templates[1]['content'] == "'{KEY}' => '{VALUE}',");
        $this->assertTrue($this->templates[1]['filename'] == '{LOCALE}/help_lang.php');

        // check param order
        $templates = $this->model->getFileTemplates('filename');
        $this->assertTrue($templates[0]['id'] == 2);

        // check param filter
        $templates = $this->model->getFileTemplates('', 'help');
        $this->assertTrue($templates[0]['id'] == 2);
        $this->assertFalse(isset($templates[1]));

        // check param recsPerPage
        $templates = $this->model->getFileTemplates('filename', '', 1, 1);
        $this->assertTrue($templates[0]['id'] == 1);
        $this->assertFalse(isset($templates[1]));
        // check rowCount: if we wouldn't have set offset to 1 we would have get 2 templates
        $this->assertEquals(2, $this->model->getRowCount());
    }

    public function testGetFileTemplateReturnsDefaultValuesOnInvalidId()
    {
        $default  = array(
            'id'       => 0,
            'name'     => '',
            'header'   => '',
            'footer'   => '',
            'content'  => '',
            'filename' => ''
        );
        $template = $this->model->getFileTemplate(-1);
        $this->assertEquals($default, $template);
    }

    public function testGetFileTemplate()
    {
        $template = $this->model->getFileTemplate(2);
        $this->assertEquals(2, $template['id']);
        $this->assertEquals('Admin help', $template['name']);
    }

    public function testGetFileTemplateAssoc()
    {
        $templates = $this->model->getFileTemplatesAssoc();

        $this->assertTrue($templates[1]['id'] == 1);
        $this->assertTrue($templates[1]['name'] == 'Admin');
        $this->assertTrue($templates[1]['content'] == "'{KEY}' => '{VALUE}',");
        $this->assertTrue($templates[1]['filename'] == '{LOCALE}/lang.php');

        $this->assertTrue($templates[2]['id'] == 2);
        $this->assertTrue($templates[2]['name'] == 'Admin help');
        $this->assertTrue($templates[2]['content'] == "'{KEY}' => '{VALUE}',");
        $this->assertTrue($templates[2]['filename'] == '{LOCALE}/help_lang.php');
        $this->assertFalse(isset($templates[0]));
        $this->assertFalse(isset($templates[3]));
    }

    public function testSaveFileTemplate()
    {
        $saved = $this->model->saveFileTemplate(127, 'test', 'header', 'content', 'footer', 'test.php');
        $this->assertNotEquals(false, $saved);
        $template = $this->model->getFileTemplate(127);
        $expected = array(
            'id'       => '127',
            'name'     => 'test',
            'header'   => 'header',
            'content'  => 'content',
            'footer'   => 'footer',
            'filename' => 'test.php'
        );
        $this->assertEquals($expected, $template);
    }

    public function testDeleteFileTemplate()
    {
        $this->model->saveFileTemplate(127, 'test', 'header', 'content', 'footer', 'test.php');
        // add a key to entries table to check if it is deleted afterwards
        $entriesModel = new Application_Model_LanguageEntries();
        $entriesModel->saveNewKey('UNIT_TEST', 127);

        $this->model->deleteFileTemplate(127);
        $template = $this->model->getFileTemplate(127);
        $this->assertTrue($template['id'] == 0);
        $key = $entriesModel->getKeyById('UNIT_TEST');
        $this->assertFalse($key);

        $this->model->saveFileTemplate(127, 'test', 'header', 'content', 'footer', 'test.php');
        // add a key to entries table to check if it is assigned to other template after deleting
        $entriesModel = new Application_Model_LanguageEntries();
        $entriesModel->saveNewKey('UNIT_TEST', 127);

        $this->model->deleteFileTemplate(127, 126);
        $template = $this->model->getFileTemplate(127);
        $this->assertTrue($template['id'] == 0);

        $key = $entriesModel->getEntryByKey('UNIT_TEST', 126);
        $this->assertTrue(isset($key['id']));
        $this->assertTrue($key !== false);
        $entriesModel->deleteEntryByKeyId($key['id']);

        // try to delete a non existent template id
        $res = $this->model->deleteFileTemplate(127, 0);
        $this->assertTrue($res['delete']);
    }

    public function testValidateDataPassesOnValidInputs()
    {
        $res = $this->model->validateData($this->tplData, $this->translator);
        $this->assertTrue($res);
        $expected = array(
            'tplFile'    => array(),
            'tplName'    => array(),
            'tplContent' => array(),
        );
        $messages = $this->model->getValidateMessages();
        $this->assertEquals($expected, $messages);
    }

    public function testValidateDataDetectsEmptyName()
    {
        $params         = $this->tplData;
        $params['name'] = '';
        $res            = $this->model->validateData($params, $this->translator);
        $this->assertFalse($res);
        $expected = 'Value is required and can\'t be empty.';
        $messages = $this->model->getValidateMessages();
        $this->assertEquals($expected, $messages['name'][0]);
    }

    public function testValidateDataDetectsEmptyFileName()
    {
        $params             = $this->tplData;
        $params['filename'] = '';
        $res                = $this->model->validateData($params, $this->translator);
        $this->assertFalse($res);
        $expected = 'Value is required and can\'t be empty.';
        $messages = $this->model->getValidateMessages();
        $this->assertEquals($expected, $messages['filename'][0]);
    }

    public function testValidateDataDetectsEmptyContent()
    {
        $params            = $this->tplData;
        $params['content'] = '';
        $res               = $this->model->validateData($params, $this->translator);
        $this->assertFalse($res);
        $expected = 'Value is required and can\'t be empty.';
        $messages = $this->model->getValidateMessages();
        $this->assertEquals($expected, $messages['content'][0]);
    }

    public function testValidateDataDetectsExistingTemplateNames()
    {
        $params         = $this->tplData;
        $params['name'] = 'Admin';
        $res            = $this->model->validateData($params, $this->translator);
        $this->assertFalse($res);
        $expected = 'A file template with the name \'Admin\' already exists.';
        $messages = $this->model->getValidateMessages();
        $this->assertEquals($expected, $messages['name'][0]);
    }

    public function testValidateDataDetectsExistingFileNames()
    {
        $params         = $this->tplData;
        $params['filename'] = '{LOCALE}/lang.php';
        $res            = $this->model->validateData($params, $this->translator);
        $this->assertFalse($res);
        $expected = 'A file template with the filename \'{LOCALE}/lang.php\' already exists.';
        $messages = $this->model->getValidateMessages();
        $this->assertEquals($expected, $messages['filename'][0]);
    }



}

<?php
/**
 * @group Models
 */
class FileTemplatesTest extends ControllerTestCase
{
    /**
     * @var \Application_Model_FileTemplates
     */
    protected $model;

    /**
     * @var array
     */
    protected $templates;

    public function setUp()
    {
        $this->model     = new Application_Model_FileTemplates();
        $this->templates = $this->model->getFileTemplates();
    }

    public function testGetFileTemplates()
    {
        $this->assertTrue($this->templates[0]['id'] == 1);
        $this->assertTrue($this->templates[0]['name'] == 'Admin');
        $this->assertTrue($this->templates[0]['content'] == "'{KEY}' => '{VALUE}',");
        $this->assertTrue($this->templates[0]['footer'] == ');');

        $this->assertTrue($this->templates[1]['id'] == 2);
        $this->assertTrue($this->templates[1]['name'] == 'Admin help');
        $this->assertTrue($this->templates[1]['content'] == "'{KEY}' => '{VALUE}',");
        $this->assertTrue($this->templates[1]['footer'] == ');');

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
        $default = array(
            'id' => 0,
            'name' => '',
            'header' => '',
            'footer' => '',
            'content' => '',
            'filename' => ''
        );
        $template = $this->model->getFileTemplate(-1);
        $this->assertEquals($default, $template);
    }

    public function testGetFileTemplate()
    {
        $template = $this->model->getFileTemplate(2);
        $this->assertEquals($this->templates[1], $template);
    }
}
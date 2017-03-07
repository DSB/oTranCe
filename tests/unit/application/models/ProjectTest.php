<?php
/**
 * @group Models
 */
class ProjectTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var \Msd_Config
     */
    protected $config;

    public function setUp()
    {
        $handler = new Msd_Config_IoHandler_Default(array(
            'directories' => TEST_PATH . '/fixtures'
        ));
        $this->config = new Msd_Config($handler);
        $this->config->load('config.ini');
    }

    public function testGetDefaultProject()
    {
        $projects = $this->config->getParam('project');
        $this->assertArrayHasKey('default', $projects);
        $default = $projects['default'];
        $this->assertSame('My Project', $default['name']);
    }

}

<?php
/**
 * @group Models
 */
class ExportLogTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Application_Model_ExportLog
     */
    protected $model;

    public function setUp()
    {
        $this->model = new Application_Model_ExportLog();
    }

    public function testAdd()
    {
        $this->model->add(9999, 'testFileName1');
        $this->model->add(9999, 'testFileName2');
        $fileList = $this->model->getFileList(9999);
        $expected = array('testFileName1', 'testFileName2');
        $this->assertEquals($expected, $fileList);
    }

    public function testDelete()
    {
        $this->model->delete(9999);
        $fileList = $this->model->getFileList(9999);
        $this->assertEquals(array(), $fileList);
    }

    public function testGetExportsCount()
    {
        $exports = $this->model->getExportsCount();
        $this->assertEquals(0, $exports);
    }
}

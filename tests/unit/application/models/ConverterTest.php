<?php
/**
 * @group Models
 */
class ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Application_Model_Converter
     */
    protected $model;

    /**
     * @var string
     */
    protected $latin1text;

    public function setUp()
    {
        $this->model      = new Application_Model_Converter();
        $this->latin1text = file_get_contents(TEST_PATH . '/fixtures/latin1Text.txt');
    }

    public function testConvertData()
    {
        $expected  = 'Ich bin ein täuschend echter latin1 text. Lümmelnde Möwen beißen';
        $converted = $this->model->convertData('latin1', $this->latin1text);
        $this->assertEquals($expected, $converted);
    }

    public function testConverterDeliversUnchangedContentIfCharsetIsUtf8()
    {
        $converted = $this->model->convertData('utf8', $this->latin1text);
        $this->assertEquals($this->latin1text, $converted);
    }

}

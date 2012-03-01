<?php
/**
 * @group Models
 */
class ConverterTest extends ControllerTestCase
{
    /**
     * @var \Application_Model_Converter
     */
    protected $model;

    public function setUp()
    {
        $this->model = new Application_Model_Converter();
    }

    public function testConvertData()
    {
        $text      = file_get_contents(TEST_PATH . '/fixtures/latin1Text.txt');
        $expected  = 'Ich bin ein täuschend echter latin1 text. Lümmelnde Möwen beißen';
        $converted = $this->model->convertData('latin1', $text);
        $this->assertEquals($expected, $converted);

    }

}
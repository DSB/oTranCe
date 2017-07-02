<?php

require_once 'Out.php';

/**
 * @group MsdViewHelper
 */

class OutTest extends PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
    }

    public function testCanReturnOriginalValue()
    {
        $expected='test';
        $res = $this->view->out('test');
        $this->assertEquals($expected, $res);
    }
    public function testCanConvertNullValue()
    {
        $expected='NULL';
        $res = $this->view->out(null, true);
        $this->assertEquals($expected, $res);
    }

    public function testCanDecorateValue()
    {
        $expected='<i>NULL</i>';
        $res = $this->view->out(null, true, 'i');
        $this->assertEquals($expected, $res);
    }
}


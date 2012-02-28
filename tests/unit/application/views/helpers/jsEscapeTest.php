<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'JsEscape.php';

/**
 * @group MsdViewHelper
 */

class JsEscapeTest extends PHPUnit_Framework_TestCase
{
    public function testCanEscape()
    {
        $expected= 'I\\\'m a message with \\\'quotes\\\' and \"double quotes\" in it.';
        $viewHelper = new Msd_View_Helper_JsEscape();
        $res = $viewHelper->jsEscape('I\'m a message with \'quotes\' and "double quotes" in it.');
        $this->assertEquals($expected, $res);
    }

}


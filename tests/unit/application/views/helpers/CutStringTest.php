<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'CutString.php';

/**
 * @group MsdViewHelper
 */

class CutStringTest extends PHPUnit_Framework_TestCase
{
    public function testCutStringSavesWords()
    {
        $expected='I will';
        $viewHelper = new Msd_View_Helper_CutString();
        $res = $viewHelper->cutString('I will be cut off', 6);
        $this->assertEquals($expected, $res);
    }

    public function testCutStringWithoutSavesWords()
    {
        $expected='I w';
        $viewHelper = new Msd_View_Helper_CutString();
        $res = $viewHelper->cutString('I will be cut off', 3, array('saveWords' => false));
        $this->assertEquals($expected, $res);
    }

    public function testCutStringCanReturnPart()
    {
        $expected='will';
        $viewHelper = new Msd_View_Helper_CutString();
        $res = $viewHelper->cutString('I will be cut off', 3, array('saveWords' => true, 'returnPart' => -1));
        $this->assertEquals($expected, $res);
    }
}


<?php

require_once 'CutString.php';

/**
 * @group MsdViewHelper
 */

class CutStringTest extends PHPUnit\Framework\TestCase
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

}


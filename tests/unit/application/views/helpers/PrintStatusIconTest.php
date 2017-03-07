<?php

require_once 'PrintStatusIcon.php';

/**
 * @group MsdViewHelper
 */

class PrintStatusIconTest extends PHPUnit\Framework\TestCase
{
    public function testCanPrintStatusIcon()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $expected='<img src="/css/otc/icons/16x16/NotOk.png" alt="" title=""/>';
        $res = $this->view->printStatusIcon(false);
        $this->assertEquals($expected, $res);

        $expected='<img src="/css/otc/icons/16x16/Apply.png" alt="" title=""/>';
        $res = $this->view->printStatusIcon(true);
        $this->assertEquals($expected, $res);
    }

    public function testCanPrintStatusIconWithTitle()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $expected='<img src="/css/otc/icons/16x16/NotOk.png" alt="testTitle" title="testTitle"/>';
        $res = $this->view->printStatusIcon(false, 'testTitle');
        $this->assertEquals($expected, $res);
    }

    public function testCanPrintStatusIconWithClass()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $expected='<img src="/css/otc/icons/16x16/NotOk.png" alt="testTitle" title="testTitle" class="myClass"/>';
        $res = $this->view->printStatusIcon(false, 'testTitle', 'myClass');
        $this->assertEquals($expected, $res);
    }

}


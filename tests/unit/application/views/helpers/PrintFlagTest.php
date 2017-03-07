<?php

include_once('ByteOutput.php');
include_once('PrintFlag.php');

/**
 * @group MsdViewHelper
 */
class PrintFlagTest extends ControllerTestCase
{
    public function testCanPrintFlag()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $res = $this->view->printFlag(1);
        $this->assertEquals('<img class="flag" src="/images/flags/de.gif" alt="Deutsch" title="Deutsch"/>', $res);
        $res = $this->view->printFlag(2);
        $this->assertEquals('<img class="flag" src="/images/flags/en.gif" alt="English" title="English"/>', $res);
    }

    public function testCanPrintFlagWithWidthSet()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $res = $this->view->printFlag(1, 20);
        $this->assertEquals('<img class="flag" src="/images/flags/de.gif" alt="Deutsch" title="Deutsch" width="20"/>', $res);
    }

    public function testCanPrintFlagWithIdSet()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $res = $this->view->printFlag(1, 20, 'myId');
        $this->assertEquals('<img class="flag" src="/images/flags/de.gif" alt="Deutsch" title="Deutsch" width="20" id="myId"/>', $res);
    }
}

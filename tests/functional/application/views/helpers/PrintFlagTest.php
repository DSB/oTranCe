<?php
require_once 'PHPUnit/Framework/TestCase.php';
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

}

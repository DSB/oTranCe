<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PrintTranslators.php';

/**
 * @group MsdViewHelper
 */

class PrintTranslatorsTest extends PHPUnit_Framework_TestCase
{
    public function testCanPrintTranslator()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $res = $this->view->printTranslators(1);
        $this->assertEquals('Admin', $res);
    }

    public function testCanPrintTranslators()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $userIds = array(1, 2);
        $res = $this->view->printTranslators($userIds);
        $this->assertEquals('Admin, tester', $res);
    }
}


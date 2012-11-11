<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PrintFileTemplate.php';

/**
 * @group MsdViewHelper
 */

class PrintFileTemplateTest extends PHPUnit_Framework_TestCase
{
    public function testCanPrintFileTemplates()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $res = $this->view->printFileTemplate(1);
        $this->assertEquals('{LOCALE}/lang.php', $res);

        $res = $this->view->printFileTemplate(2);
        $this->assertEquals('{LOCALE}/help_lang.php', $res);
    }

    public function testReturnsDashIfTemplateDoesNotExist()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $res = $this->view->printFileTemplate(-1);
        $this->assertEquals('-', $res);
    }
}


<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PrintFileTemplateHtml.php';

/**
 * @group MsdViewHelper
 */

class PrintFileTemplateHtmlTest extends PHPUnit_Framework_TestCase
{
    public function testCanPrintStatusIcon()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $expected = '<select class="select" name="fileTemplate" style="width:402px;">'
                    . '<option value="1" selected="selected">out/admin/{LOCALE}/lang.php</option>'
                    . '<option value="2">out/admin/{LOCALE}/help_lang.php</option></select>';
        $res = $this->view->printFileTemplateHtml(1);
        $this->assertEquals($expected, $res);

        $expected = '<select class="select" name="fileTemplate" style="width:402px;">'
            . '<option value="1">out/admin/{LOCALE}/lang.php</option>'
            . '<option value="2" selected="selected">out/admin/{LOCALE}/help_lang.php</option></select>';
        $res = $this->view->printFileTemplateHtml(2);
        $this->assertEquals($expected, $res);

    }

}


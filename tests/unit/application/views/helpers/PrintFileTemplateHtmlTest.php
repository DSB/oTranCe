<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PrintFileTemplateHtml.php';

/**
 * @group MsdViewHelper
 */

class PrintFileTemplateHtmlTest extends PHPUnit_Framework_TestCase
{
    public function testCanPrintFileTemplatesAsSelectList()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $expected = '<select class="select" name="fileTemplate" style="width:402px;">'
                    . '<option value="1" selected="selected">{LOCALE}/lang.php</option>'
                    . '<option value="2">{LOCALE}/help_lang.php</option></select>';
        $res = $this->view->printFileTemplateHtml(1);
        $this->assertEquals($expected, $res);

        $expected = '<select class="select" name="fileTemplate" style="width:402px;">'
            . '<option value="1">{LOCALE}/lang.php</option>'
            . '<option value="2" selected="selected">{LOCALE}/help_lang.php</option></select>';
        $res = $this->view->printFileTemplateHtml(2);
        $this->assertEquals($expected, $res);
    }

    public function testCanPrintFileTemplatesAsHiddenField()
    {
        $templateModel = new Application_Model_FileTemplates();
        $tpl           = $templateModel->getFileTemplate(1);
        $templateModel->deleteFileTemplate(1, 0);
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $expected   = '<input type="hidden" name="fileTemplate" value="2" />{LOCALE}/help_lang.php';
        $res        = $this->view->printFileTemplateHtml(1, true);
        $this->assertEquals($expected, $res);

        $saved = $templateModel->saveFileTemplate(
            $tpl['id'],
            $tpl['name'],
            $tpl['header'],
            $tpl['content'],
            $tpl['footer'],
            $tpl['filename']
        );
        // force reloading template list for following tests
        $this->view->printFileTemplateHtml(1, true);
        $this->assertTrue($saved);
    }
}


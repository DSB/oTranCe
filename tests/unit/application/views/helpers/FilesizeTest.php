<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Filesize.php';

/**
 * @group MsdViewHelper
 */
class FilesizeTest extends PHPUnit_Framework_TestCase
{
    public function testFilesize()
    {
        $expected='14.00 <span class="explain" title="Bytes">B</span>';
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $res = $this->view->filesize(APPLICATION_PATH . '/.htaccess');
        $this->assertEquals($expected, $res);
    }
}


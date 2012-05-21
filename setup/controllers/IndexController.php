<?php
class IndexController extends Setup_Controller_Abstract
{
    /**
     * Example controller action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_view->requirements = array(
            'php_version' => array(
                'title' => 'PHP-Version',
                'expected' => '5.2.10',
                'required' => true,
            ),
            'SAPI' => array(
                'title' => 'PHP SAPI',
                'expected' => 'Not ISAPI',
                'required' => false,
            ),
            'curl' => array(
                'title' => 'cURL extension',
                'expected' => 'installed',
                'required' => true,
            ),
            'mysqli' => array(
                'title' => 'MySQLi extension',
                'expected' => 'installed',
                'required' => true,
            ),
            'mcrypt' => array(
                'title' => 'mcrypt extension',
                'expected' => 'installed',
                'required' => true,
            ),
            'proc_open' => array(
                'title' => 'proc_open function',
                'expected' => 'installed',
                'required' => true,
            ),
            'zlib' => array(
                'title' => 'zlib extension',
                'expected' => 'installed',
                'required' => true,
            ),
            'zip' => array(
                'title' => 'ZIP extension',
                'expected' => 'installed',
                'required' => true,
            ),
            'ZipArchive' => array(
                'title' => 'ZIPArchive class',
                'expected' => 'installed',
                'required' => true,
            ),
            'tokenizer' => array(
                'title' => 'tokenizer extension',
                'expected' => 'installed',
                'required' => false,
            ),
            'xmlreader' => array(
                'title' => 'xmlreader extension',
                'expected' => 'installed',
                'required' => false,
            ),
        );
    }
}

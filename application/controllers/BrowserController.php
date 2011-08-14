<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Browser Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class BrowserController extends Zend_Controller_Action
{
    private $_testFiles = array();

    public function indexAction()
    {
        $fileTree = new Application_Model_FileTree(EXPORT_PATH);
        $fileTreeData = $fileTree->getJsTreeData();
        $this->view->fileTree = $fileTreeData;
        $this->view->entryCount = $fileTree->getJsTreeEntryCount();
    }

    public function fileAction()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->_response->setHeader('Content-Type', 'text/html;charset=utf-8', true);
        $filename = $this->_request->getParam('filename');
        if ($filename !== null) {
            $filename = ltrim(str_replace(EXPORT_PATH, '', $filename), '/');
            $this->view->filename = $filename;
            $this->view->fileContent = "File doesn't exists, please run export first.";
            if (file_exists(EXPORT_PATH . DS . $filename)) {
                $content = file(EXPORT_PATH . DS . $filename);
                $search  = array("\t");
                $replace = array("    ");
                $content = str_replace($search, $replace, $content);
                $this->view->fileContent = $content;
            }
        }
    }

    public function zipAction()
    {
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        Zend_Layout::getMvcInstance()->disableLayout();

        $date= date('Ymd-His');
        $archive = Msd_Archive::factory('Tar_Bz2', EXPORT_PATH . DS . "archive-$date", EXPORT_PATH);
        $archive->addFiles($this->_testFiles);
        $success = $archive->buildArchive();
        if ($success) {
            $this->_response->setHeader('Content-Type', $archive->getMimeType(), true);
            $this->_response->setHeader('Content-Disposition','attachment; filename="'
                . basename($archive->getArchiveFilename()) . '"');
            $this->_response->setBody(file_get_contents($archive->getArchiveFilename()));
        } else {
            $this->_response->setHeader('Content-Type', 'text/plain; charset=utf8', true);
            $this->_response->setBody($archive->getErrorMessage());
        }
    }
}

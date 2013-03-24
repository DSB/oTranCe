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

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $userModel = new Application_Model_User();
        if (!$userModel->hasRight('showBrowseFiles')) {
            $this->_redirect('/error/not-allowed');
        }
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $fileTree = new Application_Model_FileTree(EXPORT_PATH);
        $fileTreeData = $fileTree->getJsTreeData();
        $this->view->fileTree = $fileTreeData;
        $this->view->entryCount = $fileTree->getJsTreeEntryCount();
    }

    /**
     * Get file contents action
     *
     * @return void
     */
    public function fileAction()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->_response->setHeader('Content-Type', 'text/html;charset=utf-8', true);
        $filename = $this->_request->getParam('filename');
        if ($filename !== null) {
            $filename = ltrim(str_replace(EXPORT_PATH, '', $filename), '/');
            $this->view->filename = $filename;
            $this->view->fileContent = "File doesn't exists, please run export first.";
            if (file_exists(EXPORT_PATH . '/' . $filename)) {
                $content = file(EXPORT_PATH . '/' . $filename);
                $search  = array("\t");
                $replace = array("    ");
                $content = str_replace($search, $replace, $content);
                $this->view->fileContent = $content;
            }
        }
    }
}

<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev: 1569 $
 * @author          $Author: dsb $
 */
/**
 * Download Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class DownloadsController extends Zend_Controller_Action
{
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $userModel = new Application_Model_User();
        if (!$userModel->hasRight('showDownloads')) {
            $this->_redirect('/');
        }
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->archives = $this->_getAvailableArchives();
    }

    /**
     * Download a language pack.
     *
     * @return void
     */
    public function downloadAction()
    {
        $filename = $this->_request->getParam('file');
        if (preg_match('/^[A-Z0-9_-]+\z/i', $filename)) {
            // someone manipulated the filename. Die silently.
            die();
        }
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->_response->setHeader(
            'Content-Type',
            $this->_getArchiveContentType($filename)
        );
        $this->_response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        readfile(DOWNLOAD_PATH . '/' . $filename);
    }

    /**
     * Retrives the content type of an archive.
     *
     * @param string $filename Name of file
     *
     * @return string
     */
    private function _getArchiveContentType($filename)
    {
        if (substr($filename, -4) == '.zip') {
            return 'application/zip';
        }

        if (substr($filename, -7) == '.tar.gz') {
            return 'application/x-compressed-tar';
        }

        if (substr($filename, -8) == '.tar.bz2') {
            return 'application/x-bzip-compressed-tar';
        }

        return 'application/octet-stream';
    }

    /**
     * Creates a list with available archives.
     *
     * @return array
     */
    private function _getAvailableArchives()
    {
        $files = glob(DOWNLOAD_PATH . '/' . '{*.zip,*.tar.gz,*.tar.bz2}', GLOB_BRACE | GLOB_NOSORT);
        if (is_array($files)) {
            rsort($files);
        }
        $archives = array();
        if (empty($files)) {
            return $archives;
        }
        foreach ($files as $file) {
            $filename = str_replace(DOWNLOAD_PATH . '/', '', $file);
            $fileStats = stat($file);
            $archives[$filename] = array(
                'creationTime' => $fileStats['ctime'],
                'fileSize' => filesize($file),
            );
        }

        return $archives;
    }

}

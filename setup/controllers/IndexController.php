<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://www.otrance.org
 *
 * @package         oTranCe
 * @subpackage      Setup_Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Setup entry controller.
 *
 * @package         oTranCe
 * @subpackage      Setup_Controllers
 */
class IndexController extends Setup_Controller_Abstract
{
    /**
     * Setup entry action.
     *
     * @return void
     */
    public function indexAction()
    {
        $loadedExtensions = get_loaded_extensions();
        $installationRoot = realpath(APPLICATION_PATH . '/../..');
        $this->view->hasJsonExtension = in_array('json', $loadedExtensions);
        $this->view->hasCurlExtension = in_array('curl', $loadedExtensions);
        $this->view->installationRoot = $installationRoot;
        $this->view->rootIsWritable   = is_writable($installationRoot);
        $this->view->phpVersion       = PHP_VERSION;
        $this->view->phpVersionOk     = (version_compare(PHP_VERSION, '5.2.10') >= 0);
        $this->view->readyToInstall   = $this->view->hasJsonExtension &&
            $this->view->hasCurlExtension &&
            $this->view->rootIsWritable &&
            $this->view->phpVersionOk;
    }
}

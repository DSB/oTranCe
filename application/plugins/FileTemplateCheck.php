<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://www.otrance.org
 *
 * @package         oTranCe
 * @subpackage      Plugins
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Check if at least one file template is configured
 *
 * @package    oTranCe
 * @subpackage PlugIns
 */
class Application_Plugin_FileTemplateCheck extends Zend_Controller_Plugin_Abstract
{
    /**
     * For controllers import, export and entries we check if at least one file template is configured.
     * Otherwise we redirect to an error page with info.
     *
     * @param Zend_Controller_Request_Abstract $request The request object
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        $controllerName = $request->getControllerName();
        if (in_array($controllerName, array('entries', 'import', 'export'))) {
            $fileTemplatesModel = new Application_Model_FileTemplates();
            $fileTemplates      = $fileTemplatesModel->getFileTemplates('name');
            if (empty($fileTemplates)) {
                $controller = Zend_Controller_Front::getInstance();
                $view       = new Zend_View;
                $fullUrl    = $view->serverUrl() . $view->baseUrl() . '/error/no-file-template/';
                $controller->getResponse()->setRedirect($fullUrl);
            }
        }
    }

}

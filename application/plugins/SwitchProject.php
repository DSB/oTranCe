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
 * Check if user switched the language and save change to user config
 *
 * @package         oTranCe_Plugins
 * @subpackage      LoginCheck
 */
class Application_Plugin_SwitchProject extends Zend_Controller_Plugin_Abstract
{
    /**
     * Method will be executed before the dispatch process starts.
     *
     * @param Zend_Controller_Request_Abstract $request The request object
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $dynamicConfig         = Msd_Registry::getDynamicConfig();
        $activeProject = $dynamicConfig->getParam('activeProject');
        if (empty($activeProject)) {
            $dynamicConfig->setParam('activeProject', OtranceController::DEFAULT_PROJECT);
            $dynamicConfig->setParam('activeProjectId', OtranceController::DEFAULT_PROJECT_ID);
        }

        $switchToProject = $request->getParam('switchProject', false);
        if ($switchToProject !== false) {
            $dynamicConfig->setParam('activeProject', $switchToProject);

            // TODO this needs refactoring when defining projects in database
            $config = Msd_Registry::getConfig();
            $dynamicConfig->setParam('activeProjectId',
                $config->getParam('project')[$switchToProject]['id']);
        }
    }
}

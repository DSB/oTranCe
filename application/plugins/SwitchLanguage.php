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
class Application_Plugin_SwitchLanguage extends Zend_Controller_Plugin_Abstract
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
        $this->_setGuiLanguages();
        $switchToLanguage = $request->getParam('switchLanguage', false);
        if ($switchToLanguage !== false) {
            $this->_userModel = new Application_Model_User();
            $this->_userModel->saveSetting('interfaceLanguage', $switchToLanguage);
            $lang = Msd_Language::getInstance();
            $lang->loadLanguage($switchToLanguage);
        }
    }

    /**
     * Checks if the list of available gui languages are available in the registry.
     * If not, load them and save them to the session (for caching reasons).
     *
     *@return void
     */
    private function _setGuiLanguages()
    {
        $dynamicConfig         = Msd_Registry::getDynamicConfig();
        $availableGuiLanguages = $dynamicConfig->getParam('availableGuiLanguages', array());
        if (empty($availableGuiLanguages)) {
            $lang = Msd_Language::getInstance();
            $availableGuiLanguages = $lang->getAvailableLanguages();
            $dynamicConfig->setParam('availableGuiLanguages', $availableGuiLanguages);
        }
    }
}

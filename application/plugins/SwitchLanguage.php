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
        $dynamicConfig         = Msd_Registry::getDynamicConfig();
        $availableGuiLanguages = $dynamicConfig->getParam('availableGuiLanguages', array());
        if (empty($availableGuiLanguages)) {
            $lang = Msd_Language::getInstance();
            $availableGuiLanguages = $lang->getAvailableLanguages();
            $dynamicConfig->setParam('availableGuiLanguages', $availableGuiLanguages);
        }
        $switchToLanguage = $request->getParam('switchLanguage', false);
        if ($switchToLanguage !== false && array_key_exists($switchToLanguage, $availableGuiLanguages)) {
            $dynamicConfig->setParam('interfaceLanguage', $switchToLanguage);
        }
    }
}

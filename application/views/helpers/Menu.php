<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      View_Helpers
 * @version         SVN: $Rev$
 * @author          $Author$
 */

/**
 * Renders the menu
 *
 * @package         oTranCe
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    /**
     * Renders the menu
     *
     * @return string
     */
    public function menu()
    {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        if ($request->getActionName() == 'login') {
            //don't render menu when we show the login form
            return;
        }

        $view = $this->view;
        $view->request = $request;
        $config = Msd_Registry::getConfig();
        $view->projectConfig = $config->getParam('project');
        $menu = $view->render('index/menu.phtml');
        return $menu;
    }

}

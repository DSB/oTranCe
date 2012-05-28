<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 * @version         SVN: $Rev$
 * @author          $Author$
 */

/**
 * Print status icon
 *
 * @package         oTranCe
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_ThemeBaseUrl extends Zend_View_Helper_Abstract
{
    /**
     * Holds the url of the theme base path
     *
     * @var string
     */
    protected $_url;

    /**
     * Return the base url of the theme folder
     *
     * @return string
     */
    public function themeBaseUrl()
    {
        if ($this->_url === null) {
            $interfaceConfig = $this->view->config->getParam('interface');
            $this->_url = $this->view->baseUrl() . '/css/' . $interfaceConfig['theme'];
        }
        return $this->_url;
    }
}

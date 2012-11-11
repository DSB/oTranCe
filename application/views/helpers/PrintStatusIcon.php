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
class Msd_View_Helper_PrintStatusIcon extends Zend_View_Helper_Abstract
{
    /**
     * Print image of given locale
     *
     * @param bool   $status Status to print true/false
     * @param string $title  Title to show on hover
     * @param string $class  CSS-Class
     *
     * @return string
     */
    public function printStatusIcon($status, $title = '', $class = '')
    {
        $icon = (bool) $status ? 'Ok' : 'NotOk';
        return $this->view->getIcon($icon, $title, 16, $class);
    }
}

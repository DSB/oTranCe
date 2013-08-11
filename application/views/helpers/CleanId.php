<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://otrance.org
 *
 * @package         oTranCe
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
class Msd_View_Helper_CleanId extends Zend_View_Helper_Abstract
{
    /**
     * Replace special characters
     *
     * @param string $id The id to clean
     *
     * @return string
     */
    public function cleanId($id)
    {
        $search  = array('.');
        $replace = array('_');

        return str_replace($search, $replace, $id);
    }
}

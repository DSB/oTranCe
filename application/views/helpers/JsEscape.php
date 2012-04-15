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
 * Escape Javascript output
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_JsEscape extends Zend_View_Helper_Abstract
{
    /**
     * Escape ' and " for use in Javascript statements
     *
     * @param string $text The text to escape
     *
     * @return string
     */
    public function jsEscape($text)
    {
        $search  = array('"', "'", "\r", "\n");
        $replace = array('\"', "\'", '', '<br />');
        $text    = str_replace($search, $replace, $text);
        return $text;
    }

}

<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Models
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * Version model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_Version
{
    const VERSION = '1.1.0';

    /**
     * Get version number
     *
     * @return string
     */
    public static function getVersion()
    {
        return self::VERSION;
    }
}

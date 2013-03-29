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
 * Converter model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_Version
{
    const VERSION = '1.1.0';

    public static function getVersion()
    {
        return self::VERSION;
    }
}

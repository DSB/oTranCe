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
 * Print translator names
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_PrintTranslators extends Zend_View_Helper_Abstract
{
    /**
     * Holds all user names taken from database
     *
     * @var array
     */
    private static $_translators;

    /**
     * Print name/s of user_id's
     *
     * @param int|array $user_ids
     *
     * @return string
     */
    public function printTranslators($user_ids)
    {

        if (!is_array($user_ids)) {
            $user_ids = array($user_ids);
        }

        if (self::$_translators === null) {
            $translatorModel = new Application_Model_User();
            self::$_translators = $translatorModel->getUsers();
        }
        $ret = '';

        foreach ($user_ids as $userId) {
            if (isset(self::$_translators[$userId])) {
                $ret .= self::$_translators[$userId] .', ';
            }
        }
        $ret = substr($ret,0, -2);
        return $ret;
    }
}
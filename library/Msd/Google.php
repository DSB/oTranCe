<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Google
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Class to handle Google language mappings.
 *
 * @package         MySQLDumper
 * @subpackage      Google
 */
class Msd_Google
{
    /**
     * Get a list of translatable languages
     *
     * @static
     * @return array
     */
    public static function getTranslatableLanguages()
    {
        $ret = array('af', 'sq', 'ar', 'be', 'bg_BG', 'ca', 'zh-CN', 'zh-TW', 'hr', 'cs',
                    'da', 'nl', 'en', 'et', 'tl', 'fi', 'fr', 'gl', 'de', 'el', 'ht', 'iw',
                    'hi', 'hu', 'is', 'id', 'ga', 'it', 'ja', 'lv', 'lt', 'mk', 'ms', 'mt',
                    'no', 'fa', 'pl', 'pt_BR', 'ro', 'ru', 'sr', 'sk', 'sl', 'es', 'sw',
                    'sv_SE', 'th', 'tr', 'uk', 'vi_VN', 'cy', 'yi');
        return $ret;
    }
}

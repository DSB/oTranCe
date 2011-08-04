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
class Msd_View_Helper_PrintFlag extends Zend_View_Helper_Abstract
{
    /**
     * Holds all languages
     *
     * @var array
     */
    private static $_languages = null;

    /**
     * Print image of given locale
     *
     * @param string $langId Language locale
     * @param int    $width  Width of image
     * @param string $id     Set HTML id
     *
     * @return string
     */
    public function printFlag($langId, $width = null, $id = null)
    {
        if (self::$_languages === null) {
            $languagesModel = new Application_Model_Languages();
            self::$_languages = $languagesModel->getAllLanguages();
        }
        $ret = '';
        $langs = self::$_languages;
        if (isset(self::$_languages[$langId]) && !empty(self::$_languages[$langId]['flag_extension'])) {
            $ret = '<img src="' . $this->view->baseUrl() . '/images/flags/';
            $ret .= self::$_languages[$langId]['locale'] . '.'
                    . self::$_languages[$langId]['flag_extension'] . '"'
                    . ' alt="' . self::$_languages[$langId]['name'] . '"'
                    . ' title="' . self::$_languages[$langId]['name'] . '"';
            if ($width !== null) {
                $ret .= ' width="' . $width . '"';
            }
            if ($id !== null) {
                $ret .= ' id="' . $id . '"';
            }
            $ret .= '/>';
        }
        return $ret;
    }
}

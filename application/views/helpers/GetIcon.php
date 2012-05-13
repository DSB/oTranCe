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
 * Get img-tag for icon
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_GetIcon  extends Zend_View_Helper_Abstract
{
    private static $_iconPath = null;
    /**
     * Get html-img-tag for icon image
     *
     * @param string $name  Icon name
     * @param string $title HTML Title-Tag
     * @param int    $size  Size -> refers to a subfolder to be used
     * @param string $class CSS-Class
     * @throws Msd_Exception
     *
     * @return string
     */
    public function getIcon($name, $title = '', $size = null, $class = '')
    {
        if (self::$_iconPath === null) {
            $config = Msd_Registry::getConfig();
            $interfaceConfig = $config->getParam('interface');
            self::$_iconPath = 'css/' . $interfaceConfig['theme'] . '/icons';
        }

        static $baseUrl = false;
        if (!$baseUrl) {
            $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        }
        $icons = self::_getIconFilenames();
        if (!isset($icons[$name])) {
            throw new Msd_Exception(
                'GetIcon: unknown icon \''.$name .'\' requested'
            );
        }
        $img = '<img src="'.$baseUrl.'/%s/%s" alt="%s" title="%s"';
        if ($size !== null) {
            $img = '<img src="'.$baseUrl.'/%s/%sx%s/%s" alt="%s" title="%s"';
            $ret = sprintf(
                $img,
                self::$_iconPath,
                $size,
                $size,
                $icons[$name],
                $title, $title
            );
        } else {
            $ret = sprintf(
                $img,
                self::$_iconPath,
                $icons[$name],
                $title,
                $title
            );
        }
        if ($class > '') {
            $ret .= ' class="' . $class .'"';
        }
        $ret .= '/>';
        return $ret;
    }

    /**
     * Get default values from config.ini
     *
     * @return object
     */
    private function _getIconFilenames ()
    {
        static $icons = false;
        if (!$icons) {
            $file = realpath(
                APPLICATION_PATH . '/../public/' . self::$_iconPath . '/icon.ini'
            );
            $iconsIni = new Zend_Config_Ini($file, 'icons');
            $icons = $iconsIni->toArray();
            unset($iconsIni);
        }
        return $icons;
    }
}

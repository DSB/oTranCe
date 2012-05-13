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
 * Get img source
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_GetIconSrc  extends Zend_View_Helper_Abstract
{
    private static $_iconPath = null;
    /**
     * Get path of an image
     *
     * @throws Msd_Exception
     * @param string $name Name of icon
     * @param int    $size Size of icon -> refers to s asubfolder to be used
     *
     * @return string
     */
    public function getIconSrc($name, $size = null)
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
                'GetIconSrc: unknown icon \''.$name . '\' requested'
            );
        }
        $img = $baseUrl.'/%s/%s';
        if ($size !== null) {
            $img = $baseUrl.'/%s/%sx%s/%s';
            $ret = sprintf(
                $img,
                self::$_iconPath,
                $size,
                $size,
                $icons[$name]
            );
        } else {
            $ret = sprintf(
                $img, self::$_iconPath, $icons[$name]
            );
        }
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

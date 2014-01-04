<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://otrance.org
 *
 * @package         oTranCe
 * @subpackage      Translate
 * @author          Daniel Schlichtholz <admin@mysqldumper.de>
 */
/**
 * Translation class
 *
 * @package         oTranCe
 * @subpackage      Modules
 */
class Msd_Translate
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    private static $_loader = null;

    /**
     * Disabled class constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get translation service instance
     *
     * @param string $translationServiceName Get translation service instance
     *
     * @return bool|Module_Translate_Service_Abstract
     */
    public static function getInstance($translationServiceName = 'MyMemory')
    {
        self::_initLoader();
        // TODO read config and get correct translation service
        $translationServiceName = 'Module_Translate_Service_' . $translationServiceName;

        try {
            $translationService = new $translationServiceName;
        } catch (Exception $e) {
            $translationService = false;
        }

        return $translationService;
    }


    /**
     * Returns an array with available translation service adapters names.
     *
     * @static
     *
     * @param string $path The base path where to scan for files
     *
     * @return array
     */
    public static function getAvailableTranslationServices($path)
    {
        $dir          = $path . '/*.php';
        $files        = glob($dir);
        $serviceNames = array();
        foreach ($files as $file) {
            $serviceName = str_replace('.php', '', basename($file));
            if ($serviceName !== 'Abstract') {
                $serviceNames[] = $serviceName;
            }
        }
        sort($serviceNames, SORT_LOCALE_STRING);

        return $serviceNames;
    }

    /**
     * Initialize the plug in loader.
     *
     * @static
     *
     * @return void
     */
    private static function _initLoader()
    {
        if (self::$_loader === null) {
            self::$_loader = new Zend_Loader_PluginLoader(
                array('Module_Translate' => APPLICATION_PATH . '/../modules/library/Translate/')
            );
        }
    }


}

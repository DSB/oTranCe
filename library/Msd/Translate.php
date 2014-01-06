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
     * @static
     *
     * @param string $translationServiceName Get translation service instance
     *
     * @return bool|Module_Translate_Service_Abstract
     *
     * @throws Exception
     */
    public static function getInstance($translationServiceName = 'MyMemory')
    {
        self::_initLoader();
        $translationServiceName = 'Module_Translate_Service_' . $translationServiceName;

        try {
            $translationService = new $translationServiceName;
        } catch (Exception $e) {
            $translationService = false;
        }

        if (!$translationService instanceof Module_Translate_Service_Abstract) {
            $message = 'Couldn\'t instantiate class ' . $translationServiceName . '. Does it extend'
                . ' Module_Translate_Service_Abstract?';
            throw new Exception($message);
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

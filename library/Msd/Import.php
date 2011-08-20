<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Darky
 * Date: 20.08.11
 * Time: 16:16
 * To change this template use File | Settings | File Templates.
 */
 
class Msd_Import
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    private static $_loader = null;

    private static $_ignoreFileNames = array('Exception.php', 'Interface.php', 'Abstract.php');

    private function __construct()
    {
    }

    /**
     * Invokes and returns a new importer instance.
     *
     * @throws Msd_Import_Exception
     *
     * @param string $importerName    Name of the importer
     * @param array  $importerOptions Options for the importer
     *
     * @return Msd_Import_Interface
     */
    public static function factory($importerName, $importerOptions = null)
    {
        self::_initLoader();

        $className = self::$_loader->load($importerName);
        $importer = new $className($importerOptions);
        if (!$importer instanceof Msd_Import_Interface) {
            throw new Msd_Import_Exception(
                'Invalid importer specified. The importer must implement the Msd_Import_Interface interface.'
            );
        }

        return $importer;
    }

    public static function getAvailableImportAnalyzers()
    {
        self::_initLoader();
        $paths = self::$_loader->getPaths();
        $classes = array();
        foreach ($paths as $path) {
            if (!file_exists($path[0])) {
                continue;
            }
            $classes = array_merge($classes, self::_getDirEntries($path[0]));
        }
        return $classes;
    }

    private static function _getDirEntries($path)
    {
        $dir = new DirectoryIterator($path);
        $classes = array();
        for (; $dir->valid(); $dir->next()) {
            $filename = $dir->getFilename();
            if ($dir->isDot() || $filename{0} == '.' || in_array($filename, self::$_ignoreFileNames)) {
                continue;
            }
            if ($dir->isDir()) {
                $subdirs = self::_getDirEntries($dir->getPathname());
                foreach ($subdirs as $entry) {
                    $classes[] = $dir->getFilename() . '_' . $entry;
                }
            } else {
                $classes[] = substr($dir->getFilename(), 0, -4);
            }
        }
        return $classes;
    }

    private static function _initLoader()
    {
        if (self::$_loader === null) {
            self::$_loader = new Zend_Loader_PluginLoader(
                array(
                    'Msd_Import_' => APPLICATION_PATH . '/../library/Msd/Import/',
                    'Module_Import' => APPLICATION_PATH . '/../modules/library/Import/',
                )
            );
        }
    }
}

<?php
/**
 * Importer factory
 */
class Msd_Import
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    private static $_loader = null;

    /**
     * Ignore these filenames in the "available adapters" list.
     *
     * @var array
     */
    private static $_ignoreFileNames = array('Exception.php', 'Interface.php', 'Abstract.php');

    /**
     * Disabled class constructor.
     */
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

    /**
     * Returns an array with available analyzers.
     *
     * @static
     *
     * @return array
     */
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
        sort($classes);
        return $classes;
    }

    /**
     * Builds a list with available analyzers.
     *
     * @static
     *
     * @param string $path
     *
     * @return array
     */
    private static function _getDirEntries($path)
    {
        $dir = new DirectoryIterator($path);
        $classes = array();
        for (; $dir->valid(); $dir->next()) {
            $filename = $dir->getFilename();
            if ($dir->isDot() || $filename{0} == '.' || strripos($filename, '.phtml') !== false || in_array($filename, self::$_ignoreFileNames)) {
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

    /**
     * Initiaöoze the plug in loader.
     *
     * @static
     *
     * @return void
     */
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

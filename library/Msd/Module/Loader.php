<?php
/**
 * Autoloader implementation for module support.
 */
class Msd_Module_Loader implements Zend_Loader_Autoloader_Interface
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    private $_pluginLoader;

    /**
     * Class constructor
     *
     * @param array $prefxiesToPaths
     */
    public function __construct($prefxiesToPaths = array())
    {
        $this->_pluginLoader = new Zend_Loader_PluginLoader($prefxiesToPaths);
    }

    /**
     * Autoload a class
     *
     * @param   string $class
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    public function autoload($class)
    {
        $classToLoad = str_replace('Module_', '', $class);
        return $this->_pluginLoader->load($classToLoad);
    }

}

<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Archive
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Autoloader class for the setup application.
 *
 * @package         MySQLDumper
 * @subpackage      Archive
 */
class Setup_Loader
{
    /**
     * PHP's include paths.
     *
     * @var array
     */
    protected $_includePaths = array();

    /**
     * Class constructor. Initializes the autoloader.
     *
     * @throws \Exception
     *
     * @return \Setup_Loader
     */
    public function __construct()
    {
        $this->_includePaths = explode(PATH_SEPARATOR, get_include_path());
        if (!spl_autoload_register(array($this, 'loadClass'))) {
            require_once 'Setup/Loader/Exception.php';
            throw new Exception("Can't register autoload callback.");
        }
    }

    /**
     * Loads the PHP for a class.
     *
     * @param string $className Name of the class
     *
     * @throws Setup_Loader_Exception
     *
     * @return bool
     */
    public function loadClass($className)
    {
        $spacedClassName = str_replace('_', ' ', $className);
        $spacedClassName = ucwords($spacedClassName);
        $fileName = str_replace(' ', DIRECTORY_SEPARATOR, $spacedClassName) . '.php';
        $found = false;
        foreach ($this->_includePaths as $includePath) {
            $sanitisedPath = rtrim($includePath, '/\\') . '/';
            if (file_exists($sanitisedPath . $fileName)) {
                include_once $sanitisedPath . $fileName;

                if (class_exists($className)) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            return true;
        }

        require_once 'Setup/Loader/Exception.php';
        throw new Setup_Loader_Exception("Could not load class '$className'.");
    }
}

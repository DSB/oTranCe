<?php
class Setup_Loader
{
    protected $includePaths = array();

    public function __construct()
    {
        $this->includePaths = explode(PATH_SEPARATOR, get_include_path());
        if (!spl_autoload_register(array($this, 'loadClass'))) {
            require_once 'Setup/Loader/Exception.php';
            throw new Exception("Can't register autoload callback.");
        }
    }

    public function loadClass($className)
    {
        $spacedClassName = str_replace('_', ' ', $className);
        $spacedClassName = ucwords($spacedClassName);
        $fileName = str_replace(' ', DIRECTORY_SEPARATOR, $spacedClassName) . '.php';
        $found = false;
        foreach ($this->includePaths as $includePath) {
            $sanitisedPath = rtrim($includePath, '/\\') . '/';
            if (file_exists($sanitisedPath . $fileName)) {
                include_once $sanitisedPath . $fileName;

                if (class_exists($className)) {
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            require_once 'Setup/Loader/Exception.php';
            throw new Setup_Loader_Exception("Could not load class '$className'.");
        }
    }
}

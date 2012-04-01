<?php
//define('DS', DIRECTORY_SEPARATOR);

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('EXPORT_PATH') || define('EXPORT_PATH', realpath(APPLICATION_PATH .  '/../data/export'));
defined('DOWNLOAD_PATH') || define('DOWNLOAD_PATH', realpath(EXPORT_PATH . '/../downloads'));

// Define application environment
if (!defined('APPLICATION_ENV')) {
    $appEnvironment = getenv('APPLICATION_ENV');
    if ($appEnvironment !== false) {
        define('APPLICATION_ENV', $appEnvironment);
    } else {
        define('APPLICATION_ENV', 'production');
    }
    unset($appEnvironment);
}

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            APPLICATION_PATH,
            APPLICATION_PATH . '/models',
            get_include_path()
        )
    )
);

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();

<?php
// set up test environment

// Define path to application directory
define('DS', DIRECTORY_SEPARATOR);
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . DS . '..' . DS . 'application'));
defined('EXPORT_PATH') || define('EXPORT_PATH', realpath(APPLICATION_PATH . DS . '..' . DS . 'data' . DS . 'export'));
defined('DOWNLOAD_PATH') || define('DOWNLOAD_PATH', realpath(EXPORT_PATH . DS . '..' . DS . 'downloads'));

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . DS . '..' . DS . 'application'));

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../application'),
            realpath(APPLICATION_PATH . '/../application/controllers'),
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/views/helpers'),
            get_include_path()
        )
    )
);

// Define application environment
if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', 'production');
}

require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
clearstatcache();

include 'Cli.php';
$cli = new Cli();
$cli->updateVcs();


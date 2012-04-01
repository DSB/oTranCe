<?php
// set up test environment

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('EXPORT_PATH') || define('EXPORT_PATH', realpath(APPLICATION_PATH . '/../data/export'));
defined('DOWNLOAD_PATH') || define('DOWNLOAD_PATH', realpath(EXPORT_PATH . '/../downloads'));

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define path to test directory
defined('TEST_PATH') || define('TEST_PATH', realpath(dirname(__FILE__) . '/'));
defined('FIXTURE_PATH' || define('FIXTURE_PATH', TEST_PATH . '/fixtures'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'testing');

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/views/helpers'),
            get_include_path()
        )
    )
);

require_once 'Zend/Application.php';
require_once 'PHPUnit/Autoload.php';
require_once 'ControllerTestCase.php';
require_once 'Testhelper.php';

Testhelper::setUp();
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
Testhelper::setUpDb();
clearstatcache();

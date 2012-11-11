<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', dirname(__FILE__));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', getenv('APPLICATION_ENV'));

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/library'),
            get_include_path(),
        )
    )
);

require 'Setup/Loader.php';
$loader = new Setup_Loader();

$application = new Setup_Application(APPLICATION_PATH . '/configs/setup.ini');
$application->run();

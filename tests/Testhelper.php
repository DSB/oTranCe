<?php
class Testhelper
{
    private static $_copiedFiles = array();

    private static $_shutdownRegistered = false;

    /**
     * Prepare tests
     *
     * @return void
     */
    public static function setUp()
    {
        // create phpunit_test.ini in application/configs folder for tests
        $destinationFile = APPLICATION_PATH . '/configs/config.ini';
        if (!in_array($destinationFile, self::$_copiedFiles)) {
            self::copyFile('config.ini', $destinationFile);
        }
    }

    /**
     * Rollback actions, made by setUp() method
     *
     * @return void
     */
    public static function onShutdown()
    {
        // remove formerly copied fixture files
        foreach (self::$_copiedFiles as $copiedFile) {
            self::removeFile($copiedFile);
        }
    }

    /**
     * Copy a fixture to destination
     *
     * @param string $source      Filename of source in fixture folder
     * @param string $destination Filename of destination
     * @param bool   $overwrite   Allow an existing file to be overwritten (true) or not (false)
     * @throws Exception
     *
     * @return void
     */
    public static function copyFile($source, $destination, $overwrite = false)
    {
        $fixturePath = realpath(dirname(__FILE__) . '/fixtures');
        $source = realpath($fixturePath . '/' . $source);
        // delete target file if it exists
        if (file_exists($destination)) {
            if ($overwrite) {
                if (!unlink($destination)) {
                    throw new Exception('Error: Can\'t delete file "' . $destination .'"!');
                }
            } else {
                if (!rename($destination, $destination . '.phpunit')) {
                    throw new Exception('Error: Can\'t create backup of file "' . $destination .'"!');
                }
            }
        }
        if (!copy($source, $destination)) {
            throw new Exception(
                'Error: Can\'t copy file "' . $source . '" to "'
                . $destination .'"!'
            );
        };
        chmod($destination, 0755);
        self::$_copiedFiles[] = $destination;
        if (!self::$_shutdownRegistered) {
            register_shutdown_function(array(__CLASS__, 'onShutdown'));
            self::$_shutdownRegistered = true;
        }
    }

    /**
     * Remove a file
     *
     * @throws Exception
     * @param string $file File to remove
     *
     * @return void
     */
    public function removeFile($file)
    {
        if (!file_exists($file)) {
            return;
        }
        if (!unlink($file)) {
            throw new Exception('Error: Can\'t remove file "' . $file .'"');
        }

        if (file_exists($file . '.phpunit')) {
            if (!rename($file . '.phpunit', $file)) {
                throw new Exception('Error: Can\'t rename backup file "' . $file .'"');
            }
        }
    }

    /**
     * Executes docs/db_schema.sql and fills database
     *
     * @param string $file SQL-File to execute
     *
     * @return void
     */
    public static function setUpDb($file = 'db_schema.sql')
    {
        if (!is_readable(TEST_PATH .'/fixtures/db/' . $file)) {
            echo "\nError: couldn\' read fixture file " . $file;
            die();
        }
        $sqlFile = file_get_contents(TEST_PATH .'/fixtures/db/' . $file);
        $queries = explode(";\n", $sqlFile);
        $db = Msd_Db::getAdapter();
        $db->selectDb('phpunit_otc');
        foreach ($queries as $query) {
            if (trim($query) > '') {
                $db->query($query);
            }
        }
    }

    /**
     * Convert a MySQL Datetime type into a unix timestamp
     *
     * @static
     * @param string $datetime The MySQl-Datetime
     *
     * @return int Unix-Timestamp
     */
    public static function mysql2timestamp($datetime)
    {
       $val  = explode(' ', $datetime);
       $date = explode('-', $val[0]);
       $time = explode(':', $val[1]);
       return mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
}

}

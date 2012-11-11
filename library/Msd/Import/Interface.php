<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Import
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Interface for language file importer
 *
 * @package         MySQLDumper
 * @subpackage      Import
 */
interface Msd_Import_Interface
{
    /**
     * Analyze data and return exracted key=>value pairs
     *
     * @abstract
     * @param string $data String data to analyze
     *
     * @return array Extracted key => value-Array
     */
    public function extract($data);

    /**
     * Get rendered info view
     *
     * @param Zend_View_Interface $view View instance
     *
     * @return string
     */
    public function getInfo(Zend_View_Interface $view);

}

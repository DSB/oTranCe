<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 * @version         SVN: $Rev$
 * @author          $Author$
 */

/**
 * Print file template
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_PrintFileTemplate extends Zend_View_Helper_Abstract
{
    /**
     * Holds filetemplates id and name
     *
     * @var array
     */
    private static $_fileTemplates;

    /**
     * Print name (and id) of a file template
     *
     * @param int $fileTemplateId ID of file template
     *
     * @return string
     */
    public function printFileTemplate($fileTemplateId)
    {
        if (self::$_fileTemplates === null) {
            $templatesModel = new Application_Model_FileTemplates();
            $fileTemplates = $templatesModel->getFileTemplates();
            foreach ($fileTemplates as $template) {
                self::$_fileTemplates[$template['id']] = $template;
            }
        }
        $ret = '';

        if (isset(self::$_fileTemplates[$fileTemplateId])) {
            $ret .= self::$_fileTemplates[$fileTemplateId]['filename'];
        } else {
            $ret = '-';
        }
        return $ret;
    }
}
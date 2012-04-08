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
 * Print file template as selectbox or hidden input
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_PrintFileTemplateHtml extends Zend_View_Helper_Abstract
{
    /**
     * Holds filetemplates id and name
     *
     * @var array
     */
    private static $_fileTemplates;

    /**
     * Return list of file templates as selectbox or, if there is only one configured, as hidden input.
     *
     * @param int  $selFileTemplateId    ID of selected file template
     * @param bool $forceReloadTemplates Force reloading the list of templates
     *
     * @return string
     */
    public function printFileTemplateHtml($selFileTemplateId, $forceReloadTemplates = false)
    {
        if (self::$_fileTemplates === null || $forceReloadTemplates) {
            self::$_fileTemplates = array();
            $templatesModel = new Application_Model_FileTemplates();
            $fileTemplates = $templatesModel->getFileTemplates();
            foreach ($fileTemplates as $template) {
                self::$_fileTemplates[$template['id']] = $template;
            }
        }
        $ret = '';
        if (sizeof(self::$_fileTemplates) > 1) {
            $ret .= '<select class="select" name="fileTemplate">';
            foreach (self::$_fileTemplates as $fileTemplate) {
                $ret .= '<option value="' . $fileTemplate['id'] . '"';
                if ($selFileTemplateId == $fileTemplate['id']) {
                    $ret .= ' selected="selected"';
                }
                $ret .= '>' . $fileTemplate['filename'] . '</option>';
            }
            $ret .= '</select>';
        } elseif (sizeof(self::$_fileTemplates) == 1) {
            $values = array_values(self::$_fileTemplates);
            $ret .= '<input type="hidden" name="fileTemplate" value="' . $values[0]['id'] .'" />'
                    . $values[0]['filename'];
        }

        return $ret;
    }
}
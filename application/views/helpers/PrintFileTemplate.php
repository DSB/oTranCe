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
    private $_fileTemplates;

    /**
     * Print name (and id) of a file template
     *
     * @param int $fileTemplateId ID of file template
     *
     * @return string
     */
    public function printFileTemplate($fileTemplateId)
    {
        if ($this->_fileTemplates === null) {
            $templatesModel = new Application_Model_FileTemplates();
            $fileTemplates = $templatesModel->getFileTemplates();
            foreach ($fileTemplates as $template) {
                $this->_fileTemplates[$template['id']] = $template;
            }
        }
        $ret = '';

        if (isset($this->_fileTemplates[$fileTemplateId])) {
            $ret .= $this->_fileTemplates[$fileTemplateId]['filename'];
        } else {
            $ret = '-';
        }
        return $ret;
    }
}
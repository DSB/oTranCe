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
    private $_fileTemplates;

    /**
     * Holds the defaults for the HTML attributes of the "select" element.
     *
     * @var array
     */
    protected static $_defaultSelectHtmlAttributes = array(
        'class' => 'select',
        'name' => 'fileTemplate',
    );

    /**
     * Holds the defaults for the HTML attributes of the hidden "input" element.
     *
     * @var array
     */
    protected static $_defaultHiddenHtmlAttributes = array(
        'name' => 'fileTemplate',
        'type' => 'hidden',
    );

    /**
     * Return list of file templates as selectbox or, if there is only one configured, as hidden input.
     *
     * @param int   $selFileTemplateId ID of selected file template
     * @param array $htmlAttributes    HTML attributes for the "select" element.
     * @param bool  $replaceLocale     Replace the locale placeholder with currently selected language.
     *
     * @return string
     */
    public function printFileTemplateHtml($selFileTemplateId, $htmlAttributes = array(), $replaceLocale = false)
    {
        $this->_loadFileTemplates($replaceLocale);

        $html = '';
        if (count($this->_fileTemplates) > 1) {
            $html = $this->_buildSelectHtml($htmlAttributes, $selFileTemplateId);
        } elseif (count($this->_fileTemplates) == 1) {
            $html = $this->_buildHiddenHtml($htmlAttributes);
        }

        return $html;
    }

    /**
     * Builds the HTML code for a hidden input field.
     *
     * @param array $htmlAttributes HTML element attributes as key-value pair.
     *
     * @return string
     */
    protected function _buildHiddenHtml($htmlAttributes)
    {
        $fileTemplate = reset($this->_fileTemplates);
        $htmlAttributes = array_merge(self::$_defaultHiddenHtmlAttributes, $htmlAttributes);
        $htmlAttributes['value'] = $fileTemplate['id'];
        $html = '<input';
        $html = $this->_addHtmlAttributes($html, $htmlAttributes);
        $html .= '/> ' . $fileTemplate['filename'];
        return $html;
    }

    /**
     * Builds the HTML code for a hidden input field.
     *
     * @param array $htmlAttributes    HTML element attributes as key-value pair.
     * @param int   $selFileTemplateId ID of selected file template.
     *
     * @return string
     */
    protected function _buildSelectHtml($htmlAttributes, $selFileTemplateId)
    {
        $htmlAttributes = array_merge(self::$_defaultSelectHtmlAttributes, $htmlAttributes);
        $html = '<select';
        $html = $this->_addHtmlAttributes($html, $htmlAttributes);
        $html .= ">";
        $html = $this->_addOptions($html, $selFileTemplateId);
        $html .= '</select>';
        return $html;
    }

    /**
     * Loads the file templates from database.
     *
     * @param bool $replaceLocale Replace the locale placeholder with the currently selected language.
     *
     * @return void
     */
    protected function _loadFileTemplates($replaceLocale)
    {
        if ($this->_fileTemplates === null) {
            $this->_fileTemplates = array();
            $templatesModel = new Application_Model_FileTemplates();
            $fileTemplates = $templatesModel->getFileTemplates();
            $selectedLanguage = Msd_Registry::getDynamicConfig()->getParam('selectedLanguage');
            $languageModel = new Application_Model_Languages();
            $language = $languageModel->getLanguageById((int) $selectedLanguage);
            foreach ($fileTemplates as $template) {
                if ($replaceLocale && $selectedLanguage !== null) {
                    $template['filename'] = str_replace('{LOCALE}', $language['locale'], $template['filename']);
                }
                $this->_fileTemplates[$template['id']] = $template;
            }
        }
    }

    /**
     * Adds the HTML attributes to HTML code.
     *
     * @param string $html           Existing HTML code.
     * @param array  $htmlAttributes HTML element attributes as key-value pair.
     *
     * @return string
     */
    protected function _addHtmlAttributes($html, $htmlAttributes)
    {
        foreach ($htmlAttributes as $name => $value) {
            $html .= ' ' . $name . '="' . $value . '"';
        }
        return $html;
    }

    /**
     * Adds the select-options the HTML code.
     *
     * @param string $html              Existing HTML code.
     * @param array  $selFileTemplateId ID of the selected file template.
     * @return string
     */
    protected function _addOptions($html, $selFileTemplateId)
    {
        foreach ($this->_fileTemplates as $fileTemplate) {
            $html .= '<option value="' . $fileTemplate['id'] . '"';
            if ($selFileTemplateId == $fileTemplate['id']) {
                $html .= ' selected="selected"';
            }
            $html .= '>' . $fileTemplate['filename'] . '</option>';
        }
        return $html;
    }
}
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
        'name'  => 'fileTemplate',
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
     * @param bool  $hasNoneOption     Whether to show a "none" select option
     * @param bool  $forceReloading    Force reloading of the file template list
     *
     * @return string
     */
    public function printFileTemplateHtml(
        $selFileTemplateId,
        $htmlAttributes = array(),
        $replaceLocale = false,
        $hasNoneOption = false,
        $forceReloading = false
    )
    {
        $this->_loadFileTemplates($replaceLocale, $forceReloading);

        $html = '';
        if (count($this->_fileTemplates) > 1) {
            $html = $this->_buildSelectHtml($htmlAttributes, $selFileTemplateId, $hasNoneOption);
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
        $html = $this->_addHtmlAttributes('<input', $htmlAttributes) . '/> ' . $fileTemplate['filename'];
        return $html;
    }

    /**
     * Builds the HTML code for a hidden input field.
     *
     * @param array $htmlAttributes    HTML element attributes as key-value pair.
     * @param int   $selFileTemplateId ID of selected file template.
     * @param bool  $hasNoneOption     Whether a "none" option should be added
     *
     * @return string
     */
    protected function _buildSelectHtml($htmlAttributes, $selFileTemplateId, $hasNoneOption)
    {
        $htmlAttributes = array_merge(self::$_defaultSelectHtmlAttributes, $htmlAttributes);
        $html = $this->_addHtmlAttributes('<select', $htmlAttributes) . '>';
        $html = $this->_addOptions($html, $selFileTemplateId, $hasNoneOption) . '</select>';
        return $html;
    }

    /**
     * Loads the file templates from database.
     *
     * @param bool $replaceLocale  Replace the locale placeholder with the currently selected language.
     * @param bool $forceReloading Force reloading of file template list
     *
     * @return void
     */
    protected function _loadFileTemplates($replaceLocale, $forceReloading = false)
    {
        if ($this->_fileTemplates === null || $forceReloading) {
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
     * @param int    $selFileTemplateId ID of the selected file template.
     * @param bool   $hasNoneOption  Whether to add the selection "none"
     *
     * @return string
     */
    protected function _addOptions($html, $selFileTemplateId, $hasNoneOption)
    {
        if ($hasNoneOption) {
            $noneOption = array(
                0 => array(
                    'id' => 0,
                    'filename' => '---'
                )
            );
            $this->_fileTemplates = array_merge($noneOption, $this->_fileTemplates);
        }

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
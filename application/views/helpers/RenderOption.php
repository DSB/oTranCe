<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 * @author          Daniel Schlichtholz <admin@mysqldumper.de>
 */

/**
 * Render html option placed inside a table (used in admin area)
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_RenderOption extends Zend_View_Helper_Abstract
{
    /**
     * Render option
     *
     * @param string $id          Id of option
     * @param string $optionGroup Group of option
     * @param array  $option      Option data
     *
     * @return string
     */
    public function renderOption($id, $optionGroup, $option)
    {
        $html = '';

        switch ($option['type']) {
            case 'description':
                $html = $this->_renderDescription($option['description']);
                break;
            case 'text':
            case 'password':
                $html = $this->_renderTextInput($id, $optionGroup, $option);
                break;
        }

        return $html;
    }

    /**
     * Render simple, informational message.
     *
     * @param string $description Description language var
     *
     * @return string
     */
    protected function _renderDescription($description)
    {
        $html = '<td>' . $this->view->escape($this->view->lang->L_DESCRIPTION) . '</td>'
            . '<td>' . nl2br($this->view->escape($this->view->lang->$description)) . '</td>';

        return $html;
    }

    /**
     * Render a text input
     *
     * @param string $id          Id of option
     * @param string $optionGroup Group of option
     * @param array  $option      Option data
     *
     * @return string
     */
    protected function _renderTextInput($id, $optionGroup, $option)
    {
        $label = '';
        if (isset($option['label'])) {
            $label = $this->view->escape($this->view->lang->{$option['label']});
        }

        $description = '';
        if (isset($option['description'])) {
            $description = $this->view->escape($this->view->lang->{$option['description']});
        }

        if (!isset($option['value'])) {
            $option['value'] = $option['defaultValue'];
        }

        $html = '<td class="nowrap"><label for="' . $id . '">' . $label . '</label></td>'
            . '<td>'
            . '<input type="' . $option['type'] . '" class="text width300" id="' . $id . '"'
            . ' name="' . $optionGroup . '[' . $id . ']"'
            . ' value="' . $this->view->escape($option['value']) . '"/>';

        if ($description > '') {
            $html .= '<br />' . nl2br($description);
        }

        $html .= '</td>';

        return $html;
    }
}

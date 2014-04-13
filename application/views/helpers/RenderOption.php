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
     * Render an option
     *
     * Based on the option type a helper template is rendered.
     *
     * @param string $id          Id of option
     * @param string $optionGroup Group of option
     * @param array  $options     Option data
     *
     * @return string
     */
    public function renderOption($id, $optionGroup, $options)
    {
        $viewData = array(
            'lang'        => $this->view->lang,
            'id'          => $id,
            'options'     => $options,
            'optionGroup' => $optionGroup,
        );

        $template = 'text-input';
        switch ($options['type']) {
            case 'description':
                $template = 'description';
                break;
            case 'text':
            case 'password':
                $template = 'text-input';
                break;
        }

        return $this->view->partial('/helper/options/' . $template . '.phtml', $viewData);
    }

}

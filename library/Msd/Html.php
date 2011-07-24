<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Html
 * @version         SVN: $rev: 1207 $
 * @author          $Author$
 */
/**
 * HTML-Helper Class
 *
 * Class has some static methods for building HTML-output
 *
 * @package         MySQLDumper
 * @subpackage      Html
 */
class Msd_Html
{
    /**
     * Escape quotes and/or slashes and double quotes.
     *
     * Used for escaping strings in JS-alerts and config-files.
     *
     * @param string  $string        String to escape
     * @param boolean $escapeSlashes Escape slashes and double quotes
     *
     * @return string Escaped string
     */
    public static function getJsQuote($string, $escapeSlashes = false)
    {
        if ($escapeSlashes) {
            $string = str_replace('/', '\/', $string);
            $string = str_replace('"', '\"', $string);
        }
        $string = str_replace("\n", '\n', $string);
        return str_replace("'", '\\\'', $string);
    }

    /**
     * Build Html option string from array
     *
     * @param array   $array     Array['name'] = $val
     * @param string  $selected  Selected key
     * @param boolean $selectAll Show option to select all
     *
     * @return string Html option string
     */
    public static function getHtmlOptions($array, $selected, $selectAll = true)
    {
        $options = '';
        if ($selectAll) {
            $options = '<option value="0">---</option>'."\n";
        }
        foreach ($array as $key => $val) {
            $options .='<option value="' . $key . '"';
            if ($key == $selected) {
                $options .=' selected="selected"';
            }
            $options .='>' . $val .'</option>'."\n";
        }
        return $options;
    }

    /**
     * Build HTML option array
     *
     * @static
     * @param int $start
     * @param int $end
     * @param int $step
     * @param int $selected
     *
     * @return string
     */
    public static function getHtmlRangeOptions($start, $end, $step, $selected)
    {
        $arr = array();
        for ($i=$start; $i<=$end; $i+=$step) {
            $arr[$i] = $i;
        }
        return Msd_Html::getHtmlOptions($arr, $selected, false);
    }
}

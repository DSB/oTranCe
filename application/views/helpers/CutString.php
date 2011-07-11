<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 * @version         SVN: $Rev: 1291 $
 * @author          $Author: dsb $
 */

/**
 * Cuts a string at the specified position.
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_CutString extends Zend_View_Helper_Abstract
{
    /**
     * Default options for cutting a string. These options can be overwritten by using parameter three of the
     * cutString method.
     *
     * @var array
     */
    private $_defaults = array(
        'saveWords' => true,
        'appendString' => '',
        'returnPart' => 0,
        'escapeResult' => false,
    );
    /**
     * Cuts a string at the specified position.
     *
     * Available options are as follows:
     * saveWords    => Don't cut inside a word.
     * appendString => Append this string to the cutted string (e.g. three dots).
     * returnPart   => Part to return. Use a negative value to return all parts beginning at the specified position.
     *                 Hint: This index is zero-based.
     *
     * @param string $string  String to cut
     * @param int    $cutPos  Position to cut at
     * @param array  $options Options for cutting
     *
     * @return string
     */
    public function cutString($string, $cutPos, $options = array())
    {
        $options = array_merge($this->_defaults, $options);
        if ($options['saveWords']) {
            $wrappedString = wordwrap($string, $cutPos, '<CuTtEd>', true);
            $parts = explode('<CuTtEd>', $wrappedString);
            $cuttedString = $this->_getCuttedPart($parts, $options['returnPart']);
        } else {
            $cuttedString = substr($string, 0, $cutPos);
        }
        $cuttedString = $options['escapeResult'] ? $this->view->escape($cuttedString) : $cuttedString;
        $cuttedString .= (strlen($string) < $cutPos) ? '' : $options['appendString'];

        return $cuttedString;
    }

    /**
     * Returns the given part(s) from an array.
     *
     * @param array $cuttedParts Array with the cut parts.
     * @param int   $partNumber  Index to return.
     *                           Hint: Use a negative index to return a glued string, beginning from its positive
     *                                 position.
     *
     * @return string
     */
    private function _getCuttedPart($cuttedParts, $partNumber)
    {
        if ($partNumber >= 0) {
            return $cuttedParts[$partNumber];
        }

        $partCount = count($cuttedParts);
        $gluedParts = array();
        $partNumber = ($partNumber * -1);
        for ($i = $partNumber; $i < $partCount; $i++) {
            $gluedParts[] = $cuttedParts[$i];
        }
        return implode(' ', $gluedParts);
    }
}

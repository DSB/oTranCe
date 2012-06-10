<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Import
 * @version         SVN: $Rev: 1465 $
 * @author          $Author: kyoya $
 */
/**
 * Import class for PHP-Arrays
 *
 * @package         MySQLDumper
 * @subpackage      Import
 */
class Msd_Import_PhpArray implements Msd_Import_Interface
{
    /**
     * @var string
     */
    private $_data;

    /**
     * @var int
     */
    private $_pointer = 0;

    /**
     * @var int
     */
    private $_dataLength;

    /**
     * @var bool
     */
    private $_delimiter;

    /**
     * @var string
     */
    private $_nextToken;

    /**
     * @var bool
     */
    private $_nextKeyStarts;

    /**
     * @var int
     */
    private $_tokenStart;

    /**
     * @var int
     */
    private $_tokenEnd;

    /**
     * Will hold detected and extracted data
     * @var array
     */
    protected $_extractedData = array();

    /**
     * Array with keys we want to ignore
     * @var array
     */
    protected $_ignoreKeys = array();

    /**
     * Extract key value pairs from given string
     *
     * @param string $data The string to analyze
     *
     * @return array
     */
    public function extract($data)
    {
        $this->_data = $data;
        unset($data);
        $this->_data = preg_replace('!/\*.*?\*/!s', '', $this->_data);
        $this->_data = $this->_stripString($this->_data, '$aLang');
        $this->_data = $this->_stripString($this->_data, 'array(');
        $this->_extractedData = array();
        $this->_dataLength = strlen($this->_data);
        $this->_delimiter = false;
        $this->_nextToken = '';
        $this->_nextKeyStarts = false;

        // find key
        WHILE ($this->_pointer < $this->_dataLength) {
            $key = $this->_getKey();
            $this->_moveToNextDelimiter();
            $this->_moveToDelimiterEnd();
            WHILE (!$this->_nextKeyStarts() && $this->_pointer < $this->_dataLength) {
                $this->_moveToNextDelimiter();
                $this->_moveToDelimiterEnd();
            }
            if ($this->_pointer < $this->_dataLength) {
                $value = $this->_nextToken;
                $this->_extractedData["$key"] = $value;
            }
            $this->_nextToken = '';

        }
        $this->_removeIgnoreKeys();
        return $this->_extractedData;
    }

    /**
     * Extracts the next key.
     *
     * @return string
     */
    private function _getKey()
    {
        $this->_moveToNextDelimiter();
        $this->_moveToDelimiterEnd();
        $key = $this->_nextToken;
        $this->_nextToken = '';
        return $key;
    }

    /**
     * Moves the pointer to the next delimiter " or ' and sets the _startToken position.
     *
     * @return void
     */
    private function _moveToNextDelimiter()
    {
        $delimiterFound = false;
        $escaped = false;
        WHILE (!$delimiterFound && $this->_pointer < $this->_dataLength) {
            $char = $this->_data{$this->_pointer};
            // check for comments
            if (!$escaped && $char == '/' && $this->_data{($this->_pointer+1)} == '/') {
                // found inline comment-> move to next new line
                WHILE ($this->_data{$this->_pointer} != "\n" && $this->_pointer < $this->_dataLength) {
                    $this->_pointer++;
                }
            }
            if (!$escaped && ($char == '"' || $char == "'")) {
                $delimiterFound = true;
                $this->_delimiter = $char;
                $this->_tokenStart = $this->_pointer +1;
            }
            if ($char == '\\') {
                $escaped = true;
            } else {
                $escaped = false;
            }
            $this->_pointer++;
        }
    }

    /**
     * Returns the substring up to the next delimiter.
     *
     * @return string
     */
    private function _moveToDelimiterEnd()
    {
        $endFound = false;
        $escaped = false;
        WHILE (!$endFound && $this->_pointer < $this->_dataLength) {
            $char = $this->_data{$this->_pointer};
            if (!$escaped && $char == $this->_delimiter) {
                $endFound = true;
                $this->_tokenEnd = $this->_pointer;
            }
            if ($char == '\\') {
                $escaped = true;
            } else {
                $escaped = false;
            }
            $this->_pointer++;
        }
        $this->_nextToken .= substr($this->_data, $this->_tokenStart, $this->_tokenEnd - $this->_tokenStart);
    }

    /**
     * Detect if a neyx key starts or if we haev some string concatenation.
     *
     * @return bool
     */
    private function _nextKeyStarts()
    {
        $start = $this->_pointer;
        $this->_moveToNextDelimiter();
        $end = $this->_pointer -1;
        $charsBetweenDelimiters = substr($this->_data, $start, $end-$start);
        $charsBetweenDelimiters = $this->_removeComments($charsBetweenDelimiters);
        $ret = false;
        for ($i=0, $max = strlen($charsBetweenDelimiters); $i < $max; $i++) {
            if ($charsBetweenDelimiters{$i} == ',' || $charsBetweenDelimiters{$i} ==';') {
                $ret = true;
                break;
            }
        }
        // reset pointer to last position
        $this->_pointer = $start;
        return $ret;
    }

    /**
     * Remove comments
     *
     * @param string $string
     *
     * @return string
     */
    private function _removeComments($string)
    {
        // remove comments
        $pos = strpos($string, '//');
        if ($pos !== false) {
            $string = substr($string, 0, $pos);
        }
        return $string;
    }

    /**
     * Left trim a string
     *
     * @param string $data The string to trim
     * @param string $stripAt The string to look for and trim
     *
     * @return string
     */
    private function _stripString($data, $stripAt)
    {
        $pos = strpos($data, $stripAt);
        if ($pos !== false) {
            $data = substr($data, $pos + strlen($stripAt));
        }
        return $data;
    }

    /**
     * Remove keys we want to ignore
     *
     * @return void
     */
    private function _removeIgnoreKeys()
    {
        foreach ($this->_ignoreKeys as $ignoreKey) {
            if (isset($this->_extractedData[$ignoreKey])) {
                unset($this->_extractedData[$ignoreKey]);
            }
        }
    }
    /**
     * Get rendered info view
     *
     * @param Zend_View_Interface $view View instance
     *
     * @return string
     */
    public function getInfo(Zend_View_Interface $view)
    {
        //$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        //$view = $viewRenderer->view;
        return $view->render('import/importer/phparray.phtml');
    }

}

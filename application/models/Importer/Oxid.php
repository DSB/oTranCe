<?php

class Application_Model_Importer_Oxid implements Msd_Import_Interface
{
    /**
     * Will hold detected and extracted data
     * @var array
     */
    private $_extractedData;

    /**
     * Array with keys we want to ignore
     * @var array
     */
    private $_ignoreKeys;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_ignoreKeys = array('charset');
        $this->_extractedData = array();
    }


    /**
     * Extract key value pairs from given string
     *
     * @param string $data The string to analyze
     *
     * @return array
     */
    public function extract($data)
    {
        $this->_data = $this->stripString($data, '$aLang');
        $this->_data = $this->stripString($data, 'array(');
        $this->_pointer = 0;
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
            $value = $this->_nextToken;
            $this->_nextToken = '';

            $this->_extractedData["$key"] = $value;
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
        WHILE (!$delimiterFound && $this->_pointer < $this->_dataLength) {
            $char = $this->_data{$this->_pointer};
            if ($char == '\\') {
                $escaped = true;
            } else {
                $escaped = false;
            }

            if (!$escaped && $char == '"' || $char == "'") {
                $delimiterFound = true;
                $this->_delimiter = $char;
                $this->_tokenStart = $this->_pointer +1;
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
        WHILE (!$endFound && $this->_pointer < $this->_dataLength) {
            $char = $this->_data{$this->_pointer};
            if ($char == '\\') {
                $escaped = true;
            } else {
                $escaped = false;
            }
            if (!$escaped && $char == $this->_delimiter) {
                $endFound = true;
                $this->_tokenEnd = $this->_pointer;
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
        // remove comments
        $pos = strpos($charsBetweenDelimiters, '//');
        if ($pos !== false) {
            $charsBetweenDelimiters = substr($charsBetweenDelimiters, 0, $pos);
        }
        $ret = false;
        for ($i=0, $max = strlen($charsBetweenDelimiters); $i < $max; $i++) {
            if ($charsBetweenDelimiters{$i} == ',') {
                $ret = true;
                break;
            }
        }
        // reset pointer to last position
        $this->_pointer = $start;
        return $ret;
    }

    /**
     * Left trim a string
     *
     * @param string $data The string to trim
     * @param string $stripAt The string to look for and trim
     *
     * @return string
     */
    private function stripString($data, $stripAt)
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
    private function _removeIgnoreKeys() {
        foreach ($this->_ignoreKeys as $ignoreKey) {
            if (isset($this->_extractedData[$ignoreKey])) {
                unset($this->_extractedData[$ignoreKey]);
            }
        }
    }

}

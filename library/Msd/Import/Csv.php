<?php
/**
 * This file is part of oTranCe released under the GNU GPL 3 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Importer
 * @version         SVN: $
 * @author          $Author: $
 */

/**
 * Generic comma seperated file importer
 *
 * @package         oTranCe
 * @subpackage      Importer
 */

class Msd_Import_Csv implements Msd_Import_Interface
{
    /**
     * @var string
     */
    private $_data;

    /**
     * @var array
     */
    private $_lines;

    /**
     * @var array
     */
    private $_currentLine;

    /**
     * Will hold detected and extracted data
     * @var array
     */
    protected $_extractedData = array();

    /**
     * Key -> Value separator
     * @var string
     */
    protected $_separator = ',';

    /**
     * @var string
     */
    protected $_currentKey;

    /**
     * @var string
     */
    protected $_currentValue;

    /**
     * Analyze data and return exracted key=>value pairs
     *
     * @abstract
     * @param string $data String data to analyze
     *
     * @return array Extracted key => value-Array
     */
    public function extract($data)
    {
        $this->_data = $data;
        unset($data);
        $this->_extractedData = array();

        $this->_lines = explode("\n", $this->_data);

        for ($i = 0; $i < count($this->_lines); $i++) {
            $currentLine = explode($this->_separator, $this->_lines[$i], 2);

            $currentKey = $currentLine[0];
            $currentValue = trim($currentLine[1]);
            $dataLength = strlen($currentValue);

            if ($currentValue{0} == "'" && $currentValue{$dataLength -1} == "'" || $currentValue{0} == "\"" && $currentValue{$dataLength -1} == "\"") {
                $currentValue = substr($currentValue, 1, $dataLength - 2);
            }

            $this->_extractedData[$currentKey] = $currentValue;
        }

        return $this->_extractedData;
    }

}
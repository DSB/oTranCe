<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Importer
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * Generic comma seperated file importer
 *
 * @package         oTranCe
 * @subpackage      Importer
 */

class Module_Import_Csv implements Msd_Import_Interface
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
        $this->_data = str_replace("\xEF\xBB\xBF",'',$data); //remove bom
        unset($data);
        $this->_extractedData = array();

        $csvArray = str_getcsv($this->_data, "\n"); //parse the rows


        foreach ($csvArray AS $row){
            $currentLine = str_getcsv($row, $this->_separator);
            $currentKey = trim($currentLine[0]);

            if ($currentKey == '' || !isset($currentLine[1])) {
                continue;
            }

            $currentValue = trim($currentLine[1]);
            $currentKey = $this->cleanInput($currentKey);

            $currentValue = $this->cleanInput($currentValue);

            $this->_extractedData[$currentKey] = $currentValue;
        }

        return $this->_extractedData;
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
        return $view->render('csv.phtml');
    }

    /**
     * removes double " and single ' enclosure
     *
     * @param $inputValue
     * @return string
     */
    protected function cleanInput($inputValue)
    {
        $inputLength = strlen(trim($inputValue));
        if ($inputLength > 0){
            if (($inputValue{0} == "'" && $inputValue{$inputLength - 1} == "'")
                || ($inputValue{0} == "\"" && $inputValue{$inputLength - 1} == "\"")
            ) {
                echo "clean";
                $inputValue = substr($inputValue, 1, $inputLength - 2);
                return $inputValue;
            }
        }
        return $inputValue;
    }
}

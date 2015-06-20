<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Importer
 * @author          Tobias Rös - amicaldo GmbH
 */


class Module_Import_Json implements Msd_Import_Interface
{
    /**
     * @var string
     */
    private $_data;

    /**
     * Will hold detected and extracted data
     * @var array
     */
    protected $_extractedData = array();


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

        $matches = array();
        preg_match('/{([\s\S]*)}/', $this->_data, $matches);

        if (!empty($matches[1])){
            $json_string = $matches[0];
            if (substr(trim($json_string), 0, 1) != "{") {
                $json_string = "{" . $matches[0] . "}"; // {} was removed by parse
            }
            //@todo: more checks of malformed json and fix it
            $this->_extractedData = (array) json_decode($json_string); //decode and parse as array
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
        return $view->render('json.phtml');
    }
}

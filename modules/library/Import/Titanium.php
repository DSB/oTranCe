<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Importer
 * @version         SVN: $
 * @author          $Author: $
 */

/**
 * Importer for Titanium Studio XML files
 *
 * @package         oTranCe
 * @subpackage      Importer
 */
class Module_Import_Titanium implements Msd_Import_Interface
{

    /**
     * @var object
     */
    private $_data;

    /**
     * Will hold detected and extracted data
     * @var array
     */
    protected $_extractedData = array();

    public function extract($data)
    {
        if (!$this->_data = simplexml_load_string($data)) {
            // no valid xml file ...
            // @todo: add global handling for invalid import files in global importer
            return false;
        }
        unset($data);

        $this->_extractedData = array();

        foreach ($this->_data->children() as $node) {

            $currentKey = (string)$node->attributes()->name;
            if ($currentKey == '') {
                continue;
            }
            $currentValue = (string)$node[0];
            $this->_extractedData[$currentKey] = $currentValue;

        }

        return $this->_extractedData;
    }


    public function getInfo(Zend_View_Interface $view)
    {
        return $view->render('titanium.phtml');
    }
}

<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev: 1804 $
 * @author          $Author: dsb $
 */
/**
 * Validator for language keys.
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class Msd_Validate_LanguageKey extends Zend_Validate_Abstract
{
    /**
     * Instance of the entries model.
     *
     * @var Application_Model_LanguageEntries
     */
    protected $_entriesModel;

    /**
     * ID of the file template.
     *
     * @var int
     */
    protected $_fileTemplate;

    /**
     * Class constructor
     *
     * @param Application_Model_LanguageEntries $entriesModel Instance of the entries model.
     * @param int                               $fileTemplate ID of the file template.
     *
     * @return Msd_Validate_LanguageKey
     */
    public function __construct(Application_Model_LanguageEntries $entriesModel = null, $fileTemplate = null)
    {
        if ($entriesModel !== null) {
            $this->_entriesModel = $entriesModel;
        }

        if ($fileTemplate !== null) {
            $this->_fileTemplate = $fileTemplate;
        }
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param mixed $value Language Key to check.
     *
     * @throws Zend_Validate_Exception If validation of $value is impossible
     *
     * @return boolean
     */
    public function isValid($value)
    {
        if (strlen($value) < 1) {
            $this->_messages[] = 'Name is too short.';
            return false;
        }

        $pattern = '/^[^A-Z_]*$/';
        if (!preg_match($pattern, $value)) {
            $this->_messages[] = 'Name contains illegal characters.<br />'
                       . 'Only "A-Z" and "_" is allowed.';
            return false;
        }
        if ($this->_entriesModel !== null) {
            // check if we already have a lang var with that name
            if ($this->_entriesModel->hasEntryWithKey($value, $this->_fileTemplate)) {
                $this->_messages[] = 'A language variable with this name already exists in this file template!';
                return false;
            }
        }

        return true;
    }
}

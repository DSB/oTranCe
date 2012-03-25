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
 * Validator for user account data.
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class Msd_Validate_UserData extends Zend_Validate_Abstract
{
    /**
     * Instance of the user model.
     *
     * @var Application_Model_User
     */
    protected $_userModel;

    /**
     * Class Contructor
     *
     * @param Application_Model_User $userModel Instance of the user model.
     *
     * @return Msd_Validate_UserData
     */
    public function __construct(Application_Model_User $userModel)
    {
        $this->_userModel = $userModel;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $userData Data of the user account to check.
     *
     * @throws Zend_Validate_Exception If validation of $value is impossible
     *
     * @return boolean
     */
    public function isValid($userData)
    {
        $isValid = true;

        if ($userData['id'] == 0) {
            $notEmptyValidate = new Zend_Validate_NotEmpty();
            if (!$notEmptyValidate->isValid($userData['pass1'])) {
                // Original key: pass1
                $this->_messages['pass1'] = $notEmptyValidate->getMessages();
                $isValid = false;
            }

            // check if we already have a user with that name
            $existingUser = $this->_userModel->getUserByName($userData['username']);
            if (!empty($existingUser)) {
                // Original key: username
                $this->_messages['username'][] = 'A user with the name \'' . $userData['username'] .'\' already exists!';
                $isValid = false;
            }
        }

        $strLenValidate = new Zend_Validate_StringLength(array('min' => 2, 'max' => 50));
        if (!$strLenValidate->isValid($userData['username'])) {
            if (!isset($this->_messages['username'])) {
                $this->_messages['username'] = array();
            }
            // Original key: username
            $this->_messages['username'] = array_merge($this->_messages['username'], $strLenValidate->getMessages());
            $isValid = false;
        }

        if ($userData['pass1'] > '' || $userData['pass2'] > '') {
            $identicalValidate = new Zend_Validate_Identical($userData['pass1']);
            if (!$identicalValidate->isValid($userData['pass2'])) {
                if (!isset($this->_messages['pass1'])) {
                    $this->_messages['pass1'] = array();
                }
                // Original key: pass1
                $this->_messages['pass1'] = array_merge($this->_messages['pass1'], $identicalValidate->getMessages());
                $isValid = false;
            }
        }

        return $isValid;
    }

}

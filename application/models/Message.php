<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Models
 * @version         SVN: $
 * @author          $Author$
 */
/**
 * Error model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_Message
{
    const ERROR_SAVING = 0;
    const SAVED_SUCCESSFULLY = 1;
    const NO_PERMISSION_TO_EDIT_LANGUAGE = 2;
    const NO_PERMISSION_TO_ADD_NEW_KEY = 3;
    const VALIDATE_ERROR_NAME_TOO_SHORT = 4;

    /**
     * Mapping of error number to human readabel output
     *
     * @var array
     */
    protected $msgMap = [
            self::ERROR_SAVING                   => 'L_SAVED_UNSUCCESSFULLY',
            self::SAVED_SUCCESSFULLY             => 'L_SAVED_SUCCESSFULLY',
            self::NO_PERMISSION_TO_EDIT_LANGUAGE => 'L_YOU_ARE_NOT_ALLOWED_TO_EDIT_THIS_LANGUAGE',
            self::NO_PERMISSION_TO_ADD_NEW_KEY   => 'L_IMPORT_MISSING_PERMISSION_TO_CREATE_KEY',
            self::VALIDATE_ERROR_NAME_TOO_SHORT  => 'L_VALIDATE_ERROR_NAME_TOO_SHORT',
        ];

    /**
     * Get translation key of given error
     *
     * @param int $errorNr
     *
     * @return string
     */
    public function getTranslationKey($errorNr)
    {
        return isset($this->msgMap[$errorNr]) ? $this->msgMap[$errorNr] : 'L_ERROR';
    }
}

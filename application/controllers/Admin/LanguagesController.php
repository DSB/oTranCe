<?php
require_once('AdminController.php');
/**
 * Created by JetBrains PhpStorm.
 * User: David
 * Date: 06.07.11
 * Time: 14:26
 * To change this template use File | Settings | File Templates.
 */

class Admin_LanguagesController extends AdminController
{
    /**
     * Index action for maintaining languages
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->inputErrors = array();
        if ($this->_request->isPost()) {
            $langLocale = $this->_request->getParam('langLocale');

            $langName = $this->_request->getParam('langName');
            $upload = new Zend_File_Transfer_Adapter_Http();
            $sourceFile = $upload->getFileName();
            $targetFile = tempnam('/tmp/', 'otc');
            if (is_string(($sourceFile))) {
                $sourceExt = pathinfo($sourceFile, PATHINFO_EXTENSION);
                $targetFile = realpath(APPLICATION_PATH . '/../public/images/flags') . "/$langLocale.$sourceExt";
            }
            $upload->addFilter('Rename', array('target' => $targetFile, 'overwrite' => true));

            if ($this->_validateUserLanguageInputs($langLocale, $langName, $upload)) {
                $langModel = new Application_Model_Languages();
                $creationResult = $langModel->addLanguage($langLocale, $langName, $sourceExt);
                if ($creationResult === true) {
                    $this->view->flagFile = $upload->receive();
                }
                $this->view->creationResult = $creationResult;
            }

        }
    }

    /**
     * Validate inputs when adding a new language
     *
     * @param strin                               $langLocale Locale of language
     * @param string                              $langName   Name of language
     * @param Zend_File_Transfer_Adapter_Abstract $flag       Uploaded picture of flag
     *
     * @return bool
     */
    protected function _validateUserLanguageInputs($langLocale, $langName, Zend_File_Transfer_Adapter_Abstract $flag)
    {
        $strLenValidate = new Zend_Validate_StringLength(array('min' => 2, 'max' => 5));
        $inputsValid = true;
        $inputErrors = array();
        $langLocaleValid = $strLenValidate->isValid($langLocale);
        if (!$langLocaleValid) {
            $inputErrors['langLocale'] = $strLenValidate->getMessages();
        }
        $inputsValid &= $langLocaleValid;

        $strLenValidate->setMin(1);
        $strLenValidate->setMax(50);
        $langNameValid = $strLenValidate->isValid($langName);
        if (!$langNameValid) {
            $inputErrors['langName'] = $strLenValidate->getMessages();
        }
        $inputsValid &= $langNameValid;

        $flag->addValidator('Extension', false, array('gif', 'jpeg', 'jpg', 'png'));
        $flag->addValidator('Size', false, array('max' >= '10kB'));

        $langFlagValid = $flag->isValid();
        if (!$langFlagValid) {
            $inputErrors['langFlag'] = $flag->getMessages();
        }
        $inputsValid &= $langFlagValid;

        $this->view->inputErrors = $inputErrors;

        return $inputsValid;
    }


}

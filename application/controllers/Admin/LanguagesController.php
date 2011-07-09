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
    public function indexAction()
    {
        $recordsPerPage = (int) $this->_config->get('dynamic.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->languages = $this->_languagesModel->getAllLanguages(
            $this->_config->get('dynamic.filterUser'),
            $this->_config->get('dynamic.offset'),
            $this->_config->get('dynamic.recordsPerPage')
        );
        $this->view->hits = $this->_languagesModel->getRowCount();
    }
    /**
     * Index action for maintaining languages
     *
     * @return void
     */
    public function editAction()
    {
        $this->view->inputErrors = array();
        $id = $this->_request->getParam('id', 0);
        $intValidate = new Zend_Validate_Int();
        if (!$intValidate->isValid($id)) {
            $this->_response->setRedirect(Zend_Controller_Front::getInstance()->getBaseUrl());
        }
        $langModel = new Application_Model_Languages();
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

            if ($this->_validateUserLanguageInputs($id, $langLocale, $langName, $upload)) {
                $creationResult = $langModel->saveLanguage($id, $langLocale, $langName, $sourceExt);
                if ($creationResult === true) {
                    $this->view->flagFile = $upload->receive();
                }
                $this->view->creationResult = $creationResult;
            }
        }
        $langData = $langModel->getLanguageById($id);
        $this->view->langId = $id;
        if (count($langData) > 0) {
            $this->view->langLocale = $langData['locale'];
            $this->view->langName = $langData['name'];
            $this->view->flagExtension = $langData['flag_extension'];
        }
    }

    /**
     * Validate inputs when adding a new language
     *
     * @param int                                 $id         internal id of the language
     * @param string                              $langLocale Locale of language
     * @param string                              $langName   Name of language
     * @param Zend_File_Transfer_Adapter_Abstract $flag       Uploaded picture of flag
     *
     * @return bool
     */
    protected function _validateUserLanguageInputs($id, $langLocale, $langName, Zend_File_Transfer_Adapter_Abstract $flag)
    {
        $inputsValid = true;
        $intValidate = new Zend_Validate_Int();
        $inputsValid &= $intValidate->isValid($id);
        $strLenValidate = new Zend_Validate_StringLength(array('min' => 2, 'max' => 5));
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

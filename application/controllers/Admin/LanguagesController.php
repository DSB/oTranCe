<?php
require_once('AdminController.php');
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Languages Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class Admin_LanguagesController extends AdminController
{
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        if (!$this->_userModel->hasRight('editLanguage')) {
            $this->_redirect('/');
        }
    }

    /**
     * Retrieve data for index view.
     *
     * @return void
     */
    public function indexAction()
    {
        $deleteLanguageId = $this->_request->getParam('deleteLanguage', 0);
        if ($deleteLanguageId > 0) {
            $this->_forward('delete-language');
        }
        $recordsPerPage = (int) $this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage');
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, $recordsPerPage);
        $this->view->languages = $this->_languagesModel->getAllLanguages(
            $this->_dynamicConfig->getParam($this->_requestedController . '.filterUser'),
            $this->_dynamicConfig->getParam($this->_requestedController . '.offset'),
            $this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage'),
            false
        );
        $this->view->hits = $this->_languagesModel->getRowCount();
    }

    /**
     * Edit action for maintaining languages
     *
     * @return void
     */
    public function editAction()
    {
        $id = $this->_request->getParam('id', 0);
        $intValidate = new Zend_Validate_Int();
        if (!$intValidate->isValid($id)) {
            // someone manipulated the id of the language - silently jump to index page
            $this->_redirect('/');
        }
        if (!$this->_userModel->hasRight('addLanguage')) {
            // check if it is a new language
            $language = $this->_languagesModel->getLanguageById($id);
            if (empty($language)) {
                $this->_redirect('/');
            }
        }

        $this->view->inputErrors = array();
        $this->view->langId = $id;
        $this->view->flag   = $this->_request->getParam('flag', false);
        if ($this->_request->isPost()) {
            $this->_processInputs($id);
        } else {
            $langData = $this->_languagesModel->getLanguageById($id);
            if (count($langData) > 0) {
                $this->view->langActive    = $langData['active'];
                $this->view->langLocale    = $langData['locale'];
                $this->view->langName      = $langData['name'];
                $this->view->flagExtension = $langData['flag_extension'];
            }
        }
        $this->view->fallbackLanguageId = $this->_languagesModel->getFallbackLanguage();
    }

    /**
     * Delete a language
     *
     * @return void
     */
    public function deleteLanguageAction()
    {
        $deleteLanguageId = (int) $this->_request->getParam('deleteLanguage');
        if (!$this->_userModel->hasRight('deleteLanguage') || $deleteLanguageId < 1) {
            $this->_redirect('/admin_languages');
        }

        $res = true;
        //delete reference language settings of users
        $res &= $this->_userModel->deleteReferenceLanguageSettings($deleteLanguageId);
        //delete edit rights of language
        $res &= $this->_userModel->deleteLanguageRights($deleteLanguageId);
        //delete translations
        $res &= $this->_languageEntriesModel->deleteLanguageEntries($deleteLanguageId);
        if ($res == true) {
            $this->_deleteFlag($deleteLanguageId);
            $res = $this->_languagesModel->deleteLanguage($deleteLanguageId);
        }
        $this->view->languageDeleted = (bool) $res;
        $this->_request->setParam('deleteLanguage', 0);
        $this->_forward('index');
    }

    /**
     * Delete the flag for a language
     *
     * @return void
     */
    public function deleteFlagAction()
    {
        $languageId   = (int) $this->_request->getParam('id', 0);
        $deleteResult = 'deleted';
        if (!$this->_deleteFlag($languageId)) {
            $deleteResult = 'notDeleted';
        } else {
            //delete db entry of image
            $this->_languagesModel->deleteFlag($languageId);
        }
        $this->_forward('edit', 'admin_languages', null, array('id' => $languageId, 'flag' => $deleteResult));
    }

    /**
     * Processes inputs
     *
     * @param int $id Id of language
     *
     * @return void
     */
    public function _processInputs($id)
    {
        $langLocale = $this->_request->getParam('langLocale');
        $langActive = $this->_request->getParam('langActive', 0);
        $langName = $this->_request->getParam('langName');
        $flagUploaded = array_key_exists('langFlag', $_FILES) && ($_FILES['langFlag']['size'] > 0);
        $sourceExt = $this->_request->getParam('flagExt');
        $upload = null;
        if ($flagUploaded) {
            $upload = new Zend_File_Transfer_Adapter_Http();
            $sourceFile = $upload->getFileName();
            if (is_string($sourceFile)) {
                $sourceExt = pathinfo($sourceFile, PATHINFO_EXTENSION);
                $targetFile = realpath(APPLICATION_PATH . '/../public/images/flags') . "/$langLocale.$sourceExt";
                $upload->addFilter('Rename', array('target' => $targetFile, 'overwrite' => true));
            }
        }

        if ($this->_validateUserLanguageInputs($id, $langActive, $langLocale, $langName, $upload)) {
            $creationResult = $this->_languagesModel->saveLanguage(
                $id,
                $langActive,
                $langLocale,
                $langName,
                $sourceExt
            );
            if ($flagUploaded && $creationResult === true) {
                $this->_deleteFlags($langLocale);
                $this->view->flagFile = $upload->receive();
            }
            $this->view->creationResult = $creationResult;
            // after creating a new language clear inputs after successfull saving
            // to be able to directly input another language
            if ($creationResult === true && $id == 0) {
                $this->view->langId = 0;
                $langLocale = '';
                $langName = '';
            }
        }
        $this->view->langActive = $langActive;
        $this->view->langLocale = $langLocale;
        $this->view->langName = $langName;
        $this->view->flagExtension = $sourceExt;
    }

    /**
     * Validate inputs when adding a new language
     *
     * @param int                                 $id         Internal id of the language
     * @param int                                 $active     Active state of the language
     * @param string                              $langLocale Locale of language
     * @param string                              $langName   Name of language
     * @param Zend_File_Transfer_Adapter_Abstract $flag       Uploaded picture of flag
     *
     * @return bool
     */
    protected function _validateUserLanguageInputs(
        $id,
        $active,
        $langLocale,
        $langName,
        Zend_File_Transfer_Adapter_Abstract $flag = null
    )
    {
        $inputsValid     = true;
        $intValidate     = new Zend_Validate_Int();
        $inputsValid    &= $intValidate->isValid($id);
        $betweenValidate = new Zend_Validate_Between(array('min' => 0, 'max' => 1));
        $inputsValid    &= $intValidate->isValid($active);
        $inputsValid    &= $betweenValidate->isValid($active);
        $strLenValidate  = new Zend_Validate_StringLength(array('min' => 2, 'max' => 5));
        $inputErrors     = array();
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

        if ($flag !== null) {
            $flag->addValidator('Extension', false, array('gif', 'jpeg', 'jpg', 'png'));
            $flag->addValidator('Size', false, array('max' >= '10kB'));

            $langFlagValid = $flag->isValid();
            if (!$langFlagValid) {
                $inputErrors['langFlag'] = $flag->getMessages();
            }
            $inputsValid &= $langFlagValid;
        }

        $this->view->inputErrors = $inputErrors;

        return $inputsValid;
    }

    /**
     * Deletes all flag images for the given locale.
     *
     * @param string $locale Language locale.
     *
     * @return bool
     */
    private function _deleteFlags($locale)
    {
        $result = true;
        $flagFiles = glob(realpath(APPLICATION_PATH . '/../public/images/flags') . "/$locale.*");
        if (empty($flagFiles)) {
            //nothing to delete
            return true;
        }

        foreach ($flagFiles as $flagFile) {
            $result &= @unlink($flagFile);
        }
        return $result;
    }

    /**
     * Delete the flag image file of the given language from disk
     *
     * @param int $languageId Id of language
     *
     * @return bool
     */
    protected function _deleteFlag($languageId)
    {
        $lang      = $this->_languagesModel->getLanguageById($languageId);
        $imageFile = realpath(APPLICATION_PATH . '/../public/images/flags')
                        . "/{$lang['locale']}.{$lang['flag_extension']}";
        return @unlink($imageFile);
    }
}

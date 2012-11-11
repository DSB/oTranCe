<?php
require_once('AdminController.php');
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Languages Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
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
            $this->_dynamicConfig->getParam($this->_requestedController . '.filterLanguage'),
            $this->_dynamicConfig->getParam($this->_requestedController . '.offset'),
            $this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage'),
            false
        );
        $this->view->hits               = $this->_languagesModel->getRowCount();
        $this->view->fallbackLanguageId = $this->_languagesModel->getFallbackLanguageId();
    }

    /**
     * Edit action for maintaining languages
     *
     * @return void
     */
    public function editAction()
    {
        $languageId  = (int) $this->_request->getParam('id', 0);
        if ($languageId < 0) {
            // someone manipulated the id of the language - silently jump to index page
            $this->_redirect('/');
        }

        // check if it is a new language and if the user has the right to add a new language
        if (!$this->_userModel->hasRight('addLanguage')) {
            $language = $this->_languagesModel->getLanguageById($languageId);
            if (empty($language)) {
                $this->_redirect('/');
            }
        }

        $this->view->inputErrors = array();
        $this->view->languageId  = $languageId;
        $this->view->flag        = $this->_request->getParam('flag', false);
        if ($this->_request->isPost()) {
            $languageId = $this->_processInputs($languageId);
        } else {
            $langData = $this->_languagesModel->getLanguageById($languageId);
            if (count($langData) > 0) {
                $this->view->langActive    = $langData['active'];
                $this->view->langLocale    = $langData['locale'];
                $this->view->langName      = $langData['name'];
                $this->view->flagExtension = $langData['flag_extension'];
            }
        }

        if ($languageId > 0) {
            $this->_assignUserList($languageId);
        }
        $flagImage = realpath(APPLICATION_PATH . '/../public/images/flags') . '/' . $this->view->langLocale . '.'
            . $this->view->flagExtension;
        if (!is_readable($flagImage)) {
            $this->view->flagExtension = '';
        }

        $this->view->languageId         = $languageId;
        $this->view->fallbackLanguageId = $this->_languagesModel->getFallbackLanguageId();
    }

    /**
     * If language is available show user list to set edit rights
     *
     * @param int $languageId The Id of the language
     *
     * @return void
     */
    protected function _assignUserList($languageId)
    {
        $this->view->filterUser = $this->_dynamicConfig->getParam($this->_requestedController . '.filterUser');
        $this->view->users = $this->_userModel->getUsers(
            (string)$this->_dynamicConfig->getParam($this->_requestedController . '.filterUser'),
            (int)   $this->_dynamicConfig->getParam($this->_requestedController . '.offset'),
            (int)   $this->_dynamicConfig->getParam($this->_requestedController . '.recordsPerPage')
        );
        $this->view->userHits = $this->_userModel->getRowCount();

        $translators = $this->_userModel->getTranslators($languageId);
        $translators = empty($translators[$languageId]) ? array() : $translators[$languageId];
        //set user id as index - this way we can use isset() for testing if a user id has edit rights
        // (instead of searching with in_array() which is slower)
        $translators = array_flip($translators);
        $this->view->translators = $translators;
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int)$this->view->recordsPerPage);
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
        //trigger ajax call to optimize database tables
        $this->_dynamicConfig->setParam('optimizeTables', true);

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
     * @param int $languageId Id of language
     *
     * @return int $languageId Id of the current language
     */
    public function _processInputs($languageId)
    {
        $langLocale   = $this->_request->getParam('langLocale');
        $langActive   = $this->_request->getParam('langActive', 0);
        $langName     = $this->_request->getParam('langName');
        $flagUploaded = array_key_exists('langFlag', $_FILES) && ($_FILES['langFlag']['size'] > 0);

        $sourceExt = $this->_request->getParam('flagExt');
        $upload = null;
        if ($flagUploaded) {
            $upload     = new Zend_File_Transfer_Adapter_Http();
            $sourceFile = $upload->getFileName();
            if (is_string($sourceFile)) {
                $sourceExt = pathinfo($sourceFile, PATHINFO_EXTENSION);
                $targetFile = realpath(APPLICATION_PATH . '/../public/images/flags') . "/$langLocale.$sourceExt";
                $upload->addFilter('Rename', array('target' => $targetFile, 'overwrite' => true));
            }
        }
        $this->view->langActive    = $langActive;
        $this->view->langLocale    = $langLocale;
        $this->view->langName      = $langName;
        $this->view->flagExtension = $sourceExt;
        $this->view->languageId    = $languageId;

        // check if something was changed in the language form
        if (!$flagUploaded && $languageId > 0) {
            $lang = $this->_languagesModel->getLanguageById($languageId);
            if ($lang['locale'] == $langLocale && $lang['active'] == $langActive && $lang['name'] == $langName) {
                return $languageId;
            }
        }

        if ($this->_validateUserLanguageInputs($languageId, $langActive, $langLocale, $langName, $upload)) {
            $creationResult = $this->_languagesModel->saveLanguage(
                $languageId,
                $langActive,
                $langLocale,
                $langName,
                $sourceExt
            );

            if ($flagUploaded === false && $languageId > 0 && $lang['locale'] != $langLocale) {
                // locale of existing language was changed -> rename existing flag image
                $path = realpath(APPLICATION_PATH . '/../public/images/flags') . '/';
                $newFileName = $path . $langLocale . '.' . $lang['flag_extension'];
                $oldFileName = $path . $lang['locale'] . '.' . $lang['flag_extension'];
                @rename($oldFileName, $newFileName);
            }

            //if it is was a new langauge - get id
            if ($languageId == 0) {
                $languageId = $this->_languagesModel->getLastInsertedId();
            }

            if ($flagUploaded && $creationResult === true) {
                $this->_deleteFlags($langLocale);
                $this->view->flagFile = $upload->receive();
            }
            $this->view->creationResult = $creationResult;
        }

        return $languageId;
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
        $translator = Msd_Language::getInstance();
        $inputsValid     = true;
        $intValidate     = new Zend_Validate_Int();
        $inputsValid    &= $intValidate->isValid($id);

        $betweenValidate = new Zend_Validate_Between(array('min' => 0, 'max' => 1));
        $inputsValid    &= $intValidate->isValid($active);
        $inputsValid    &= $betweenValidate->isValid($active);

        $strLenValidate  = new Zend_Validate_StringLength(array('min' => 2, 'max' => 5));
        $inputErrors     = array();
        $langLocaleValid = $strLenValidate->isValid($langLocale);
        $inputErrors['langLocale'] = array();
        if (!$langLocaleValid) {
            $inputErrors['langLocale'] = $translator->translateZendMessageIds($strLenValidate->getMessages());
        }
        $inputsValid &= $langLocaleValid;

        if ($inputsValid) {
            // Check if this locale already exists
            $tempLangId = $this->_languagesModel->getLanguageIdFromLocale($langLocale);
            if ( ($id == 0 && $tempLangId > 0) || ($id > 0 && $tempLangId >0 && $tempLangId != $id)) {
                $inputErrors['langLocale'][] = $translator->translate('L_LOCALE_EXISTS');
                $inputsValid = false;
            }

            $charValidate = new Zend_Validate_Regex(array('pattern' => '/^[a-zA-Z0-9\.\-_]+$/i'));
            $langLocaleValid = $charValidate->isValid($langLocale);
            if (!$langLocaleValid) {
                $inputErrors['langLocale'][] = $translator->translate('L_ERROR_INVALID_CHARS');
            }
            $inputsValid &= $langLocaleValid;
        }

        $strLenValidate->setMin(1);
        $strLenValidate->setMax(50);
        $langNameValid = $strLenValidate->isValid($langName);
        if (!$langNameValid) {
            $inputErrors['langName'] = $translator->translateZendMessageIds($strLenValidate->getMessages());
        }
        $inputsValid &= $langNameValid;

        if ($flag !== null && $inputsValid != false) {
            $flag->addValidator('Extension', false, array('gif', 'jpeg', 'jpg', 'png'));
            $flag->addValidator('Size', false, '20480');

            $langFlagValid = $flag->isValid();
            if (!$langFlagValid) {
                $inputErrors['langFlag'] = $translator->translateZendMessageIds($flag->getMessages());
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
     * Delete the flag image file of the given language id from disk
     *
     * @param int $languageId Id of language
     *
     * @return bool
     */
    protected function _deleteFlag($languageId)
    {
        $lang = $this->_languagesModel->getLanguageById($languageId);
        if (!isset($lang['locale'])) {
            // language doesn't exist - nothing to delete
            return true;
        }
        $imageFile = realpath(APPLICATION_PATH . '/../public/images/flags')
                        . "/{$lang['locale']}.{$lang['flag_extension']}";
        return @unlink($imageFile);
    }
}

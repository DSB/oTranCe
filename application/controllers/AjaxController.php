<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Ajax Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class AjaxController extends OtranceController
{
    /**
     * Languages model
     *
     * @var \Application_Model_Languages
     */
    protected $_languagesModel;

    /**
     * Languages entries model
     *
     * @var \Application_Model_LanguageEntries
     */
    protected $_entriesModel;

    /**
     * Array holding all languages
     *
     * @var array
     */
    protected $_languages;

    /**
     * The fallback language
     *
     * @var string
     */
    protected $_fallbackLanguage;

    /**
     * The fallback language data holding the values of the given keys
     *
     * @var array
     */
    protected $_fallbackLanguageData;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_languagesModel = new Application_Model_Languages();
        $this->_languages      = $this->_languagesModel->getAllLanguages();
        $this->_entriesModel   = new Application_Model_LanguageEntries();
    }

    /**
     * Translate an entry using Google translate
     *
     * @return void
     */
    public function translateAction()
    {
        $keyId            = $this->_request->getParam('key');
        $sourceLang       = $this->_request->getParam('source');
        $targetLang       = $this->_request->getParam('target');
        $entry            = $this->_entriesModel->getTranslationsByKeyId($keyId, array($sourceLang));
        $this->view->data = $this->_getTranslation(
            $entry[$sourceLang],
            $this->_languages[$sourceLang]['locale'],
            $this->_languages[$targetLang]['locale']
        );
    }

    /**
     * Import action.
     * Expects array of language entry keys as param in request and returns an array(key => status).
     * Status:
     *  0 = technical error
     *  1 = saved successfully
     *  2 = user has no edit right for this language
     *  3 = user is not allowed to add this new entry
     *
     * @return void
     */
    public function importKeyAction()
    {
        $ret           = array();
        $params        = $this->_request->getParams();
        $languageId    = $params['languageId'];
        $fileTemplate  = $params['fileTemplate'];
        $keys          = $params['keys'];
        $this->_data   = $this->_dynamicConfig->getParam('extractedData');
        $i             = 0;
        $fallbackData  = $this->_getFallbackLanguageData($keys, $fileTemplate, $languageId);
        $overallResult = true;
        foreach ($keys as $key) {
            $saveKey = true;
            if (!empty($fallbackData[$key]) && $fallbackData[$key] == $this->_data[$key]) {
                // value is the same as in the fallback language
                // check if user is allowed to import such phrases
                $saveKey = false;
                if ($this->_userModel->hasRight('importEqualVar')) {
                    $saveKey = true;
                }
            }

            if ($saveKey === false) {
                $ret[$i] = array('id' => md5($key), 'result' => 4);
            } else {
                $res = $this->_saveKey($key, $fileTemplate, $languageId);
                if ($res !== 1) {
                    $overallResult = false;
                }
                $ret[$i] = array('id' => 'k' . md5($key), 'result' => $res);
            }
            $i++;
        }

        if ($overallResult === true) {
            //remove saved keys from session to speed up things
            //dont do it if an error occured because the user can click on "retry". We need the data in this case.
            foreach ($keys as $key) {
                unset($this->data[$key]);
            }
            $this->_dynamicConfig->setParam('extractedData', $this->_data);
        }

        $this->view->data = $ret;
    }

    /**
     * Triggers optimization of all database tables.
     * This is done after delete operations.
     *
     * @return void
     */
    public function optimizeTablesAction()
    {
        $results = $this->_languagesModel->optimizeAllTables();
        $ret     = array();
        foreach ($results as $res) {
            if (in_array($res['Msg_type'], array('status', 'info'))) {
                //ok
                $ret[$res['Table']] = 'ok';
            } else {
                //error - get info
                $ret[$res['Table']] = $res['Msg_text'];
            }
        }
        $this->view->data = $ret;
    }

    /**
     * Switch the language edit right of a user
     *
     * @return void
     */
    public function switchLanguageEditRightAction()
    {
        $languageId = (int)$this->_request->getParam('languageId', 0);
        $userId     = (int)$this->_request->getParam('userId', 0);
        if ($userId < 1 || $languageId < 1 || !$this->_userModel->hasRight('editUsers')) {
            //Missing param or no permission to change edit right
            $icon = $this->view->getIcon('Attention', $this->view->lang->L_ERROR, 16);
        } else {
            //revert actual right
            $languageEditRight = !$this->_userModel->hasLanguageEditRight($userId, $languageId);
            if ($languageEditRight == false) {
                //delete right
                $res = $this->_userModel->deleteUsersEditLanguageRight($userId, $languageId);
            } else {
                //add right
                $res = $this->_userModel->addUsersEditLanguageRight($userId, $languageId);
            }

            if ($res == true) {
                if ($languageEditRight == false) {
                    $icon = $this->view->getIcon('NotOk', $this->view->lang->L_CHANGE_RIGHT, 16);
                } else {
                    $icon = $this->view->getIcon('Ok', $this->view->lang->L_CHANGE_RIGHT, 16);
                    // inform user via e-mail that his account has been activated
                    $user     = $this->_userModel->getUserById($userId);
                    $language = $this->_languagesModel->getLanguageById($languageId);
                    $mail     = new Application_Model_Mail($this->view);
                    $mail->sendEditRightGrantedMail($user, $language);
                }
            } else {
                $icon = $this->view->getIcon('Attention', $this->view->lang->L_ERROR_SAVING_LANGUAGE_EDIT_RIGHT, 16);
            }
        }

        $this->view->data = array('icon' => $icon);
        $this->render('json');
    }

    /**
     * Set/unset the right of a user
     *
     * @return void
     */
    public function switchRightAction()
    {
        $right  = (string)$this->_request->getParam('right', '');
        $userId = (int)$this->_request->getParam('userId', 0);
        $icon   = $this->view->getIcon('NotOk', $this->view->lang->L_CHANGE_RIGHT, 16);
        if ($userId < 1 || $right == '' || !$this->_userModel->hasRight('editUsers')) {
            //Missing param or no permission to change edit right
            $data = array('error' => 'Invalid arguments', 'icon' => $icon);
        } else {
            //get actual right
            $userRights = $this->_userModel->getUserRights($userId);
            if ($userRights[$right] > 0) {
                //delete right
                $res = $this->_userModel->saveRight($userId, $right, 0);
            } else {
                //add right
                $res = $this->_userModel->saveRight($userId, $right, 1);
                if ($res == true) {
                    $icon = $this->view->getIcon('Ok', $this->view->lang->L_CHANGE_RIGHT, 16);
                };
            }

            if ($res == true) {
                $data = array('error' => false, 'icon' => $icon);
            } else {
                //error saving
                $data = array('error' => $this->view->lang->L_ERROR_SAVING_RIGHT, 'icon' => $icon);
            }
        }

        $this->view->data = $data;
        $this->render('json');
    }

    /**
     * Activate/deactivate a language
     *
     * @return void
     */
    public function switchLanguageStatusAction()
    {
        $languageId         = (int)$this->_request->getParam('languageId', 0);
        $icon               = $this->view->getIcon('Attention', $this->view->lang->L_ERROR, 16);
        $fallbackLanguageId = $this->_languagesModel->getFallbackLanguageId();
        if ($languageId < 1 || $languageId == $fallbackLanguageId || !$this->_userModel->hasRight('editLanguage')) {
            //Missing param or no permission to change status
            $data = array('icon' => $icon);
        } else {
            //get actual language
            $language = $this->_languagesModel->getLanguageById($languageId);
            //switch status
            $language['active'] = ($language['active'] > 0) ? 0 : 1;
            $res                = $this->_languagesModel->saveLanguageStatus($languageId, $language['active']);
            if ($res === true) {
                if ($language['active'] > 0) {
                    $icon = $this->view->getIcon('Ok', $this->view->lang->L_CHANGE_STATUS, 16);
                } else {
                    $icon = $this->view->getIcon('NotOk', $this->view->lang->L_CHANGE_STATUS, 16);
                }
                $data = array('icon' => $icon);
            } else {
                $data = array('icon' => $icon);
            }
        }

        $this->view->data = $data;
        $this->render('json');
    }

    /**
     * Save the name of a key (used at inline editing)
     *
     * @return void
     */
    public function saveKeyNameAction()
    {
        $keyId   = (int)substr($this->_request->getParam('id'), 4);
        $keyName = (string)$this->_request->getParam('new_value');
        $keyName = strip_tags($keyName);
        $ret     = array('is_error' => false);
        $errors  = array();

        //check rights
        if (!$this->_userModel->hasRight('editKey') || $keyId == 0) {
            $errors[] = $this->view->lang->L_YOU_ARE_NOT_ALLOWED_TO_DO_THIS;
        } else {
            // we need to get the assigned file template for validating the keys name
            $entry = $this->_entriesModel->getKeyById($keyId);
            if (!$this->_entriesModel->validateLanguageKey($keyName, $entry['template_id'])) {
                $errors = array_merge($errors, $this->_entriesModel->getValidateMessages());
            }

            if (empty($errors)) {
                //everything is ok -> save
                $saved = $this->_entriesModel->updateKeyName($keyId, $keyName);
                if ($saved === false) {
                    $errors[] = $this->view->lang->L_ERROR_SAVING_KEY;
                } else {
                    $historyModel = new Application_Model_History();
                    $historyModel->logVarNameChanged($keyId, $entry['key'], $keyName);
                }
            }
        }

        // re-read the saved key name to get the real value from the database
        $newKey      = $this->_entriesModel->getKeyById($keyId);
        $ret['html'] = htmlspecialchars($newKey['key'], ENT_COMPAT, 'UTF-8');
        if (!empty($errors)) {
            $ret['is_error']   = true;
            $ret['error_text'] = implode('<br />', $errors);
        }

        $this->view->data = $ret;
        $this->render('json');
    }

    /**
     * Save a translation (used at inline editing)
     *
     * @return void
     */
    public function saveTranslationAction()
    {
        $param       = (string)$this->_request->getParam('id', '');
        $params      = explode('-', $param);
        $keyId       = !empty($params[1]) ? $params[1] : 0;
        $languageId  = !empty($params[2]) ? $params[2] : 0;
        $translation = (string)$this->_request->getParam('new_value');
        $ret         = array('is_error' => false, 'add_needs_update_flags' => false);
        $errors      = array();

        //check rights
        $editLanguages = $this->_userModel->getUserLanguageRights();
        if (!in_array($languageId, $editLanguages)
            || $keyId == 0 || $languageId == 0
        ) {
            $errors[] = $this->view->lang->L_YOU_ARE_NOT_ALLOWED_TO_DO_THIS;
        } else {
            $data     = array($languageId => $translation);
            $fallback = $this->_languagesModel->getFallbackLanguageId();
            $saved    = $this->_entriesModel->saveEntries($keyId, $data, $fallback);
            if ($saved !== true) {
                $errors[] = $this->view->lang->L_ERROR_SAVING_CHANGE;
            } elseif ($fallback == $languageId) {
                $ret['add_needs_update_flags'] = true;
            }
        }

        // re-read the saved translation to get the real value from the database
        $translations = $this->_entriesModel->getTranslationsByKeyId($keyId, $languageId);
        $ret['html']  = htmlspecialchars($translations[$languageId], ENT_COMPAT, 'UTF-8');
        if (!empty($errors)) {
            $ret['is_error']   = true;
            $ret['error_text'] = implode('<br />', $errors);
        }

        $this->view->data = $ret;
        $this->render('json');
    }

    /**
     * Activate/deactivate a user, depending on the current status.
     *
     * @return void
     */
    public function switchUserStatusAction()
    {
        $userId = (int)$this->_request->getParam('userId', false);
        $icon   = $this->view->getIcon('Attention', $this->view->lang->L_ERROR, 16);
        if ($userId < 1 || !$this->_userModel->hasRight('editUsers')) {
            //Missing param or no permission to change status
            $data = array('icon' => $icon);
        } else {
            //get user data
            $user = $this->_userModel->getUserById($userId);
            //switch status
            $user['active'] = ($user['active'] > 0) ? 0 : 1;
            unset($user['password']);
            $user['pass1'] = '';
            $res           = $this->_userModel->saveAccount($user);
            $historyModel  = new Application_Model_History();
            if ($res !== false) {
                if ($user['active'] > 0) {
                    $icon = $this->view->getIcon('Ok', $this->view->lang->L_CHANGE_STATUS, 16);
                    // inform user via e-mail that his account has been activated
                    $mailer = new Application_Model_Mail($this->view);
                    $mailer->sendAccountActivationInfoMail($user);
                    $historyModel->logUserAccountApproved($user['id']);
                } else {
                    $historyModel->logUserAccountClosed($user['id']);
                    $icon = $this->view->getIcon('NotOk', $this->view->lang->L_CHANGE_STATUS, 16);
                }
            }
            $data = array('icon' => $icon);
        }
        $this->view->data = $data;
        $this->render('json');
    }

    /**
     * Enabe/disable a language as reference language for a user
     *
     * @return void
     */
    public function switchReferenceLanguageStatusAction()
    {
        $userId     = $this->_userModel->getUserId();
        $languageId = (int)$this->_request->getParam('languageId');
        $icon       = $this->view->getIcon('Attention', $this->view->lang->L_ERROR, 16);
        if ($languageId > 0) {
            $status = $this->_userModel->switchReferenceLanguageStatus($userId, $languageId);
            if ($status === true) {
                $icon = $this->view->getIcon('Ok', $this->view->lang->L_CHANGE_STATUS, 16);
            } else {
                $icon = $this->view->getIcon('NotOk', $this->view->lang->L_CHANGE_STATUS, 16);
            }
        }
        $data             = array('icon' => $icon);
        $this->view->data = $data;
        $this->render('json');
    }

    /**
     * Uploads a new project logo.
     *
     * @return void
     */
    public function uploadProjectLogoAction()
    {
        /**
         * @var Zend_Controller_Request_Http $request
         */
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);

        $request         = $this->getRequest();
        $publicDir       = realpath(APPLICATION_PATH . '/../public');
        $interfaceConfig = $this->_config->getParam('interface');
        $webPath         = '/css/' . $interfaceConfig['theme'] . '/pics';
        $targetDir       = $publicDir . $webPath;


        if ($request->isXmlHttpRequest()) {
            $uploader = new Msd_Upload_Xhr($targetDir);
        } else {
            $uploader = new Msd_Upload_Form($targetDir);
        }

        $result            = array(
            'success' => $uploader->isFileTypeAllowed(array('png', 'jpg', 'jpeg', 'jpe', 'gif'))
        );
        $result['success'] = $result['success'] && $uploader->saveFile();
        if ($result['success']) {
            $result['newLogo'] = $webPath . '/' . $uploader->getFilename();

            // Save the uploaded image as project logo in config. Uncomment these lines to activate automatic saving.
            if ((bool)$this->_request->getParam('saveToConfig', false)) {
                $projectConfig                  = $this->_config->getParam('project');
                $projectConfig['logo']['large'] = $result['newLogo'];
                $this->_config->setParam('project', $projectConfig);
                $this->_config->save();
            }
        }

        $this->_response->setBody(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
    }

    /**
     * User request for edit right for the given language.
     *
     * @return void
     */
    public function requestLanguageEditRightAction()
    {
        $auth     = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $userData = $this->_userModel->getUserById($identity['id']);

        $locale     = $this->_request->getParam('locale');
        $languageId = $this->_languagesModel->getLanguageIdFromLocale($locale);
        $language   = $this->_languagesModel->getLanguageById($languageId);
        $data       = array('success' => false);
        if ($languageId > 0 && $userData['id'] > 0) {
            // send mail to admin
            $mail = new Application_Model_Mail($this->view);
            $mail->sendEditRightRequestedMail($userData, $language);
            $data['success'] = true;
        }
        $this->view->data = $data;
        $this->render('json');
    }

    /**
     * Activate/deactivate an importer, depending on the current status.
     *
     * @return void
     */
    public function switchImporterStatusAction()
    {
        $importer      = (string)$this->_request->getParam('importer', '');
        $icon          = $this->view->getIcon('Attention', $this->view->lang->L_ERROR, 16);
        $importerModel = new Application_Model_Importers();
        $allImporters  = $importerModel->getImporter();

        //check rights and if such an importer exists
        if (!$this->_userModel->hasRight('editImporter') || !isset($allImporters["$importer"])) {
            $data = array('icon' => $icon);
        } else {
            //switch status
            $allImporters[$importer] = $allImporters[$importer] == 1 ? 0 : 1;
            $this->_config->setParam('importers', $allImporters);
            $res = $this->_config->save();
            if ($res !== false) {
                if ($allImporters[$importer] == 1) {
                    $icon = $this->view->getIcon('Ok', $this->view->lang->L_CHANGE_STATUS, 16);
                } else {
                    $icon = $this->view->getIcon('NotOk', $this->view->lang->L_CHANGE_STATUS, 16);
                }
            }
            $data = array('icon' => $icon);
        }

        $this->view->data = $data;
        $this->render('json');
    }

    /**
     * Set the standard importer
     *
     * @return void
     */
    public function setStandardImporterAction()
    {
        $importerId = (string)$this->_request->getParam('importer', '');
        $icon       = $this->view->getIcon('Attention', $this->view->lang->L_ERROR, 16);

        if ($this->_userModel->hasRight('editImporter')) {
            $importerModel = new Application_Model_Importers();
            $res           = $importerModel->setStandardImporter($importerId);
            $icon          = $this->view->getIcon('Ok', $this->view->lang->L_SELECT_AS_STANDARD, 16);
            if ($res === false) {
                $icon = $this->view->getIcon('Attention', $this->view->lang->L_ERROR, 16);
            }
        }

        $this->view->data = array('icon' => $icon);
        $this->render('json');
    }

    /**
     * Save a key and it's value to the database.
     *
     * @param string $key          Keyname to save
     * @param int    $fileTemplate Id of the file template
     * @param int    $languageId   Id of language
     *
     * @return int
     */
    private function _saveKey($key, $fileTemplate, $languageId)
    {
        // check edit right for language
        $userEditRights = $this->_userModel->getUserLanguageRights();
        if (!in_array($languageId, $userEditRights)) {
            //user is not allowed to edit this language
            return 2;
        }

        if (!$this->_entriesModel->hasEntryWithKey($key, $fileTemplate)) {
            //it is a new entry - check rights
            if (!$this->_userModel->hasRight('addVar')) {
                return 3;
            } else {
                // Validate the new key.
                if (!$this->_entriesModel->validateLanguageKey($key, $fileTemplate)) {
                    return 4;
                }
                // user is allowed to add new keys -> create it
                $this->_entriesModel->saveNewKey($key, $fileTemplate);
            }
        }

        // ok - we can save the value -> key id
        $entry = $this->_entriesModel->getEntryByKey($key, $fileTemplate);
        $keyId = $entry['id'];
        $value = $this->_data[$key];
        $res   = $this->_entriesModel->saveEntries($keyId, array($languageId => $value));
        if ($res === true) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get the translations of the keys for the fallbackLanguage
     *
     * @param array $keys       The language keys
     * @param int   $templateId Id of the file template
     * @param int   $languageId Id of the language
     *
     * @return array|bool
     */
    private function _getFallbackLanguageData($keys, $templateId, $languageId)
    {
        $fallbackLanguageId = $this->_languagesModel->getFallbackLanguageId();
        if ($fallbackLanguageId == $languageId) {
            // imported language is the fallback language - nothing to check
            return false;
        }

        return $this->_entriesModel->getEntriesByKeys($keys, $templateId, $fallbackLanguageId);
    }

    /**
     * Translate given text into target language using the translation service provider
     *
     * @param string $text       The text to translate
     * @param string $sourceLang Source locale
     * @param string $targetLang Target locale
     *
     * @return string
     */
    private function _getTranslation($text, $sourceLang, $targetLang)
    {
        if ($text == '') {
            return '';
        }

        $translationService = Otc_Translate::getInstance();
        $translatedText     = $translationService->getTranslation($text, $sourceLang, $targetLang);

        return $translatedText;
    }

}

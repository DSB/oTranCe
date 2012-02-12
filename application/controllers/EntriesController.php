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
 * Entries Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class EntriesController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_LanguageEntries
     */
    private $_entriesModel;

    /**
      * @var Application_Model_Languages
      */
    private $_languagesModel;

     /**
     * @var Application_Model_User
     */
    private $_userModel;

    /**
     * @var Msd_Config
     */
    protected $_config;

    /**
     * @var Msd_Config_Dynamic
     */
    protected $_dynamicConfig;

    /**
     * @var array
     */
    private $_languagesEdit = array();

    /**
     * @var array
     */
    private $_showLanguages = array();

    /**
     * @var array
     */
    private $_referenceLanguages = array();

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_entriesModel = new Application_Model_LanguageEntries();
        $this->_userModel = new Application_Model_User();
        $this->view->user = $this->_userModel;

        $this->_dynamicConfig = Msd_Registry::getDynamicConfig();
        $this->_config = Msd_Registry::getConfig();
        $this->_languagesModel = new Application_Model_Languages();
        $this->_setSessionParams();
    }

    /**
     * Handle index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_getParams();
        if ($this->_request->isPost() && $this->_request->getParam('forwarded') == null) {
            if ($this->_request->getParam('addVar') !== null) {
                $this->_forward('add-variable');
            }
        } else {
            $this->_setSessionParams();
        }
        $this->_assignVars();
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int)$this->view->recordsPerPage);
        $this->setLanguages();
        $filterLanguageArray = $this->_languagesModel->getAllLanguages();
        $this->view->selLanguage = Msd_Html::getHtmlOptionsFromAssocArray(
            $filterLanguageArray,
            'id',
            '{name} ({locale})',
            $this->_dynamicConfig->getParam('entries.getUntranslated'),
            false
        );

        // assign file template filter
        $fileTemplateFilter = $this->_dynamicConfig->getParam('entries.fileTemplateFilter');
        $this->view->fileTemplateFilter = $fileTemplateFilter;
        $fileTemplatesModel = new Application_Model_FileTemplates();
        $fileTemplates = $fileTemplatesModel->getFileTemplates('name');
        $this->view->selFileTemplate =
                Msd_Html::getHtmlOptionsFromAssocArray($fileTemplates, 'id', 'filename', $fileTemplateFilter);

        if ($this->view->getUntranslated == 0) {
            if ($this->view->filterKeys > '' || $this->view->fileTemplateFilter > 0) {
                $this->view->hits = $this->_entriesModel->getEntriesByKey(
                    $this->view->filterKeys,
                    $this->view->offset,
                    $this->view->recordsPerPage,
                    $this->view->fileTemplateFilter
                );
            } else {
                $this->view->hits = $this->_entriesModel->getEntriesByValue(
                    $this->view->showLanguages,
                    $this->view->filterValues,
                    $this->view->offset,
                    $this->view->recordsPerPage,
                    $this->view->fileTemplateFilter
                );
            }
        } else {
            $languageId = $this->_dynamicConfig->getParam('entries.getUntranslated');
            $this->view->hits =
                    $this->_entriesModel->getUntranslated(
                        $languageId,
                        $this->view->filter,
                        $this->view->offset,
                        $this->view->recordsPerPage,
                        $this->view->fileTemplateFilter
                    );
        }

        $this->view->rows = $this->_entriesModel->getRowCount();

        $this->view->hits = $this->_entriesModel->assignTranslations(
            $this->view->showLanguages,
            $this->view->hits
        );
    }

    /**
     * Get and set language params in view and in private properties
     * (Languages to edit, references and which to show in list view)
     *
     * @return void
     */
    public function setLanguages()
    {
        $this->view->languages     = $this->_languagesModel->getAllLanguages();
        $this->_languagesEdit      = $this->getEditLanguages();
        $this->view->languagesEdit = $this->_languagesEdit;
        $userModel = new Application_Model_User();

        // get reference languages and make sure that the fallback language is at top
        $this->_referenceLanguages      = array(0 => $this->_languagesModel->getFallbackLanguage());
        $this->_referenceLanguages      = array_merge($this->_referenceLanguages, $userModel->getRefLanguages());
        $this->_referenceLanguages      = array_unique($this->_referenceLanguages);
        $this->view->referenceLanguages = $this->_referenceLanguages;

        // build show language array for index page
        $this->_showLanguages      = $this->_languagesEdit;
        $this->_showLanguages      = array_merge($this->_showLanguages, $this->_referenceLanguages);
        $this->_showLanguages      = array_unique($this->_showLanguages);
        $this->view->showLanguages = $this->_showLanguages;
    }

    /**
     * Handle edit action
     *
     * @return void
     */
    public function editAction()
    {
        $id = $this->_request->getParam('id');
        if ($this->_request->isPost() && $this->_request->getParam('forwarded') == null) {
            // editing canceled
            if ($this->_request->getParam('cancel') != null) {
                $this->_myForward('index');
                return;
            }
            // in all other cases save changes first
            $this->view->entrySaved = $this->_saveEntries();
            if ($this->view->entrySaved == 1) {
                // return to entry list
                if ($this->_request->getParam('saveReturn') !== null) {
                    $this->_myForward('index');
                    return;
                }
                // Get next untranslated var
                if ($this->view->entrySaved !== false && $this->_request->getParam('saveUntranslated') != null) {
                    $nextId = $this->_findNextUntranslated();
                    if ($nextId !== null) {
                        $id = $nextId;
                    } else {
                        $this->view->noUntranslatedFound = true;
                    }
                }
            }
        }
        $this->setLanguages();
        $this->view->key   = $this->_entriesModel->getKeyById($id);
        $this->view->entry = $this->_entriesModel->getEntryById($id, $this->_showLanguages);
        $this->view->user  = $this->_userModel;

        $templatesModel = new Application_Model_FileTemplates();
        $this->view->fileTemplates        = $templatesModel->getFileTemplates('name');
        $this->view->assignedFileTemplate = $this->_entriesModel->getAssignedFileTemplate($id);
        $this->view->translatable         = Msd_Google::getTranslatableLanguages();
    }

    /**
     * Find next untranslated language variable and return its Id.
     *
     * Iterates over all languages the user is allowed to edit.
     *
     * @return int|null
     */
    private function _findNextUntranslated()
    {
        $nextId = null;
        $langEnriesModel = new Application_Model_LanguageEntries();
        $languages = $this->getEditLanguages();
        foreach ($languages as $lang) {
            $nextId = $langEnriesModel->getFirstUntranslated($lang);
            if ($nextId !== null) {
                break;
            }
        }
        return $nextId;
    }
    /**
     * Handle delete action
     *
     * @return void
     */
    public function deleteAction()
    {
        // check, that the user really has delete rights
        if ($this->_userModel->hasRight('addVar')) {
            $id = $this->_request->getParam('id');
            $entry = $this->_entriesModel->getKeyById($id);
            $res = $this->_entriesModel->deleteEntryByKeyId($id);
            if ($res) {
                $historyModel = new Application_Model_History();
                $historyModel->logVarDeleted($entry['key']);
            }
        }
        $this->_myForward('index');
    }

    /**
     * Get post params and set to config which is saved top session
     *
     * @return void
     */
    private function _getParams()
    {
        $filterValues       = trim($this->_request->getParam('filterValues', ''));
        $filterKeys         = trim($this->_request->getParam('filterKeys', ''));
        $offset             = (int) $this->_request->getParam('offset', 0);
        $recordsPerPage     = (int) $this->_request->getParam('recordsPerPage', 0);
        // will be not 0 if set and set to id of language to search in
        $getUntranslated    = (int) $this->_request->getParam('getUntranslated', 0);
        $fileTemplateFilter = (int) $this->_request->getParam('fileTemplateFilter', 0);
        $this->_dynamicConfig->setParam('entries.offset', $offset);
        $this->_dynamicConfig->setParam('entries.filterValues', $filterValues);
        $this->_dynamicConfig->setParam('entries.filterKeys', $filterKeys);
        $this->_dynamicConfig->setParam('entries.recordsPerPage', $recordsPerPage);
        $this->_dynamicConfig->setParam('entries.getUntranslated', $getUntranslated);
        $this->_dynamicConfig->setParam('entries.fileTemplateFilter', $fileTemplateFilter);
    }

    /**
     * Set default session values on first page call
     *
     * @return void
     */
    private function _setSessionParams()
    {
        // set defaults on first page call
        if ($this->_dynamicConfig->getParam('entries.recordsPerPage') == 0) {
            $this->_dynamicConfig->setParam('entries.offset', 0);
            $this->_dynamicConfig->setParam('entries.filterValues', '');
            $this->_dynamicConfig->setParam('entries.filterKeys', '');
            $recordsPerPage = $this->_userModel->loadSetting('entries.recordsPerPage', 20);
            $this->_dynamicConfig->setParam('entries.recordsPerPage', $recordsPerPage);
            $this->view->getUntranslated = $this->_dynamicConfig->getParam('entries.getUntranslated');
            $this->view->addVar = $this->_userModel->hasRight('addVar');
        }
    }

    /**
     * Assign params to view (formerly taken from post or session)
     *
     * @return void
     */
    private function _assignVars()
    {
        $this->view->filterValues    = $this->_dynamicConfig->getParam('entries.filterValues');
        $this->view->filterKeys      = $this->_dynamicConfig->getParam('entries.filterKeys');
        $this->view->offset          = $this->_dynamicConfig->getParam('entries.offset');
        $this->view->recordsPerPage  = $this->_dynamicConfig->getParam('entries.recordsPerPage');
        $this->view->getUntranslated = $this->_dynamicConfig->getParam('entries.getUntranslated');
        $this->view->addVar          = $this->_userModel->hasRight('addVar');

    }

    /**
     * Save and log changes
     *
     * @return bool
     */
    private function _saveEntries()
    {
        $params = $this->getRequest()->getParams();
        $values = array();
        foreach ($params as $name => $val) {
            if (substr($name, 0, 5) == 'edit-') {
                $values[substr($name, 5)] = trim($val);
            }
        }
        $res = true;
        if (isset($params['fileTemplate'])) {
            $res &= $this->_entriesModel->assignFileTemplate($params['id'], $params['fileTemplate']);
        }
        $res &= $this->_entriesModel->saveEntries((int)$params['id'], $values);
        return $res;
    }

    /**
     * Get list of edit languages
     *
     * @return array
     */
    public function getEditLanguages()
    {
        $userModel = new Application_Model_User();
        return $userModel->getUserLanguageRights();
    }

    /**
     * Get an entry by it's database id
     *
     * @param int $id Id of entry record
     *
     * @return array
     */
    public function getEntryById($id)
    {
        return $this->_entriesModel->getEntryById($id, $this->_showLanguages);
    }

    /**
     * Add a new language variable
     *
     * @return void
     */
    public function addVariableAction()
    {
        $error = array();
        if (!$this->_userModel->hasRight('addVar')) {
            $error = array('You are not allowed to add a new language variable!');
        }
        $newVar = '';
        $fileTemplate = $this->_request->getParam(
            'fileTemplate',
            $this->_dynamicConfig->getParam('entries.addVar.fileTemplate')
        );
        $this->_dynamicConfig->setParam('entries.addVar.fileTemplate', $fileTemplate);
        if (empty($error) && $this->_request->isPost() && $this->_request->getParam('var') !== null) {
            if ($this->_request->getParam('cancel') != null) {
                $this->_myForward('index');
                return;
            }
            $newVar = trim($this->_request->getParam('var'));
            $this->_dynamicConfig->setParam('entries.fileTemplate', $fileTemplate);
            if (strlen($newVar) < 1) {
                $error[] = 'Name is too short.';
            }
            $pattern = '/[^A-Z_]/i';
            if (preg_replace($pattern, '', $newVar) !== $newVar) {
                $error[] = 'Name contains illegal characters.<br />'
                           . 'Only "A-Z" and "_" is allowed.';
            }
            // check if we already have a lang var with that name
            if ($this->_entriesModel->hasEntryWithKey($newVar, $fileTemplate)) {
                $error = array('A language variable with this name already exists in this file template!');
            }
            if (empty($error)) {
                try {
                    $this->_entriesModel->saveNewKey($newVar, $fileTemplate);
                    $entry = $this->_entriesModel->getEntryByKey($newVar, $fileTemplate);
                    $historyModel = new Application_Model_History();
                    $historyModel->logNewVarCreated($entry['id']);
                    $this->view->entry = $entry;
                    $this->_request->setParam('id', $entry['id']);
                    $this->_myForward('edit');
                    return;
                } catch (Exception $e) {
                    $error = array('Technical error: Couldn\'t create new language variable.<br />' . $e->getMessage());
                }
            }
        }
        if (!empty($error)) {
            $this->view->error = implode('<br />', $error);
        }
        $this->view->newVar               = $newVar;
        $fileTemplatesModel               = new Application_Model_FileTemplates();
        $this->view->fileTemplates        = $fileTemplatesModel->getFileTemplates('name');
        $this->view->fileTemplateSelected = $this->_dynamicConfig->getParam('entries.addVar.fileTemplate', 0);
    }

    /**
     * Set forward flag and forward to action
     *
     * @param  string $action
     *
     * @return void
     */
    private function _myForward($action)
    {
        $this->_request->setParam('forwarded', true);
        $this->_forward($action);
    }
}

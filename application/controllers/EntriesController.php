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
     * @var Msd_Configuration
     */
    private $_config;

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
        $this->_config = Msd_Configuration::getInstance();
        $this->_languagesModel = new Application_Model_Languages();
    }

    /**
     * Handle index action
     *
     * @return void
     */
    public function indexAction()
    {
        if ($this->_request->isPost() && $this->_request->getParam('forwarded') == null) {
            $this->_getPostParams();
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
            $this->_config->get('dynamic.getUntranslated'),
            false
        );

        // assign file template filter
        $fileTemplateFilter = $this->_config->get('dynamic.fileTemplateFilter');
        $this->view->fileTemplateFilter = $fileTemplateFilter;
        $fileTemplatesModel = new Application_Model_FileTemplates();
        $fileTemplates = $fileTemplatesModel->getFileTemplates('name');
        $this->view->selFileTemplate = Msd_Html::getHtmlOptionsFromAssocArray($fileTemplates, 'id', 'filename', $fileTemplateFilter);

        if ($this->view->getUntranslated == 0) {
            $this->view->hits =
                    $this->_entriesModel->getEntries(
                        $this->_showLanguages,
                        $this->view->filter,
                        $this->view->offset,
                        $this->view->recordsPerPage,
                        $this->view->fileTemplateFilter
                    );
        } else {
            $languageId = $this->_config->get('dynamic.getUntranslated');
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
    }

    /**
     * Get and set language params in view and in private properties
     * (Languages to edit, references and which to show in list view)
     *
     * @return void
     */
    public function setLanguages()
    {
        $this->view->languages = $this->_languagesModel->getAllLanguages();
        $this->_languagesEdit = $this->getEditLanguages();
        $this->view->languagesEdit = $this->_languagesEdit;
        $this->_showLanguages = $this->_languagesEdit;
        $userModel = new Application_Model_User();
        $this->_referenceLanguages = $userModel->getRefLanguages();
        if (is_array($this->_referenceLanguages)) {
            $this->_showLanguages = array_merge($this->_showLanguages, $this->_referenceLanguages);
            $this->_showLanguages = array_unique($this->_showLanguages);
        }
        $this->view->referenceLanguages = $this->_referenceLanguages;
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
                //TODO Implement button "get next untranslated"
                if ($this->_request->getParam('saveUntranslated') != null) {
                }
            }
        }
        $this->setLanguages();
        $this->view->key = $this->_entriesModel->getKeyById($id);
        $this->view->entry = $this->_entriesModel->getEntryById($id, $this->_showLanguages);
        $this->view->user = $this->_userModel;
        $templatesModel = new Application_Model_FileTemplates();
        $this->view->fileTemplates = $templatesModel->getFileTemplates('name');
        $this->view->assignedFileTemplate = $this->_entriesModel->getAssignedFileTemplate($id);
        $this->view->translatable = Msd_Google::getTranslatableLanguages();
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
    private function _getPostParams()
    {
        $filter             = trim($this->_request->getParam('filter', ''));
        $offset             = (int) $this->_request->getParam('offset', 0);
        $recordsPerPage     = (int) $this->_request->getParam('recordsPerPage', 0);
        // will be not 0 if set and set to id of language to search in
        $getUntranslated    = (int) $this->_request->getParam('getUntranslated', 0);
        $fileTemplateFilter = (int) $this->_request->getParam('fileTemplateFilter', 0);
        $this->_config->set('dynamic.offset', $offset);
        $this->_config->set('dynamic.filter', $filter);
        $this->_config->set('dynamic.recordsPerPage', $recordsPerPage);
        $this->_config->set('dynamic.getUntranslated', $getUntranslated);
        $this->_config->set('dynamic.fileTemplateFilter', $fileTemplateFilter);
    }

    /**
     * Set default session values on first page call
     *
     * @return void
     */
    private function _setSessionParams()
    {
        // set defaults on first page call
        if ($this->_config->get('dynamic.offset') === null) {
            $this->_config->set('dynamic.offset', 0);
            $this->_config->set('dynamic.filter', '');
            $recordsPerPage = $this->_userModel->loadSetting('recordsPerPage', 20);
            $this->_config->set('dynamic.recordsPerPage', $recordsPerPage);
            $this->view->getUntranslated = $this->_config->get('dynamic.getUntranslated');
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
        $this->view->filter          = $this->_config->get('dynamic.filter');
        $this->view->offset          = $this->_config->get('dynamic.offset');
        $this->view->recordsPerPage  = $this->_config->get('dynamic.recordsPerPage');
        $this->view->getUntranslated = $this->_config->get('dynamic.getUntranslated');
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
        return $userModel->getUserEditRights();
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
        $newVar = 'L_';

        if (empty($error) && $this->_request->isPost() && $this->_request->getParam('var') !== null) {
            if ($this->_request->getParam('cancel') != null) {
                $this->_myForward('index');
                return;
            }
            $newVar = trim($this->_request->getParam('var'));
            if (strlen($newVar) < 6) {
                $error[] = 'Name is too short.';
            }
            if (substr($newVar, 0, 2) != 'L_') {
                $error[] = 'Illegal prefix! Name must begin with "L_".';
            }
            $pattern = '/[^A-Z_]/';
            if (preg_replace($pattern, '', $newVar) !== $newVar) {
                $error[] = 'Name contains illegal characters.<br />'
                           . 'Only "A-Z" and "_" is allowed.';
            }
            // check if we already have a lang var with that name
            //TODO check for unique combination of key and file template!
            if ($this->_entriesModel->hasEntryWithKey($newVar)) {
                $error = array('A language variable with this name already exists!');
            }
            if (empty($error)) {
                try {
                    $fileTemplate = $this->_request->getParam('fileTemplate', 0);
                    $this->_entriesModel->saveNewKey($newVar, $fileTemplate);
                    $historyModel = new Application_Model_History();
                    $entry = $this->_entriesModel->getEntryByKey($newVar);
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
        $this->view->newVar        = $newVar;
        $fileTemplatesModel        = new Application_Model_FileTemplates();
        $this->view->fileTemplates = $fileTemplatesModel->getFileTemplates('name');
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

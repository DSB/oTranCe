<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Settings controller
 *
 * Controller to handle actions triggered on index screen
 *
 * @package         MySQLDumper
 * @subpackage      Controllers
 */
class SettingsController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_User
     */
    private $_userModel;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_userModel = new Application_Model_User();
    }

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $languagesModel = new Application_Model_Languages();
        $this->view->languages = $languagesModel->getAllLanguages();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $languagesSelected = array();
            $params = array_keys($request->getParams());
            foreach ($params as $val) {
                if (substr($val, 0, 5) == 'lang_') {
                    $languagesSelected[] = substr($val, 5);
                }
            }
            $recordsPerPage = $request->getParam('recordsPerPage', 20);
            //save new settings to session
            $config = Msd_Configuration::getInstance();
            $config->set('dynamic.recordsPerPage', $recordsPerPage);

            $saved = $this->saveUserSettings($languagesSelected, $recordsPerPage);
            $this->view->saved = $saved;
        } else {
            $recordsPerPage = $this->_userModel->loadSetting('recordsPerPage', 10);
            $languagesSelected = $this->getRefLanguageSettings();
        }
        $this->view->selRecordsPerPage = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int) $recordsPerPage);
        $this->view->refLanguagesSelected = $languagesSelected;
        $this->view->editLanguages = $this->_userModel->getUserEditRights();
    }

    /**
     * Save list of reference languages
     *
     * @param array $languagesSelected
     * @param int   $recordsPerPage
     *
     * @return boolean
     */
    public function saveUserSettings($languagesSelected, $recordsPerPage)
    {
        $res = $this->_userModel->saveSetting('recordsPerPage', $recordsPerPage);
        $res &= $this->_userModel->saveSetting('referenceLanguage', $languagesSelected);
        return $res;
    }

    /**
     * Get list of reference languages
     *
     * @return boolean
     */
    public function getRefLanguageSettings()
    {
        $res = $this->_userModel->loadSetting('referenceLanguage', '', true);
        return $res;
    }

}

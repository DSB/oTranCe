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
 * Settings Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class SettingsController extends Msd_Controller_Action
{
    /**
     * @var Application_Model_User
     */
    protected $_userModel;

    /**
     * @var array
     */
    protected $_projectConfig;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->_userModel = new Application_Model_User();
        if (!$this->_userModel->hasRight('editConfig')) {
            $this->_redirect('/');
        }
        $this->_projectConfig = $this->_config->getParam('project');
        $this->view->vcsActivated = false;
        if ($this->_projectConfig['vcsActivated'] == 1) {
            $this->view->vcsActivated = true;
        }
    }

    /**
     * Process index action
     *
     * @return void
     */
    public function indexAction()
    {
        $languagesModel        = new Application_Model_Languages();
        $interfaceLanguage     = $this->_dynamicConfig->getParam('interfaceLanguage', false);
        $userInterfaceLanguage = $this->_userModel->loadSetting('interfaceLanguage');
        $languageConfig        = Msd_Language::getInstance($interfaceLanguage);

        if ($this->_request->isPost()) {
            $recordsPerPage    = $this->_request->getParam('recordsPerPage', 20);
            $interfaceLanguage = $this->_request->getParam('interfaceLanguage', $interfaceLanguage);
            $this->_dynamicConfig->setParam('interfaceLanguage', $interfaceLanguage);
            $saved                 = $this->saveUserSettings($recordsPerPage, $interfaceLanguage);
            $userInterfaceLanguage = $this->_userModel->loadSetting('interfaceLanguage');
            $this->view->saved = (bool) $saved;
        } else {
            $recordsPerPage = $this->_userModel->loadSetting('recordsPerPage', 10);
        }
        $this->view->languages            = $languagesModel->getAllLanguages();
        $this->view->selRecordsPerPage    = Msd_Html::getHtmlRangeOptions(10, 200, 10, (int) $recordsPerPage);
        $availableLanguages               = $languageConfig->getAvailableLanguages();
        $this->view->selInterfaceLanguage = Msd_Html::getHtmlOptionsFromAssocArray(
            $availableLanguages,
            'locale',
            '{locale} - {name}',
            $userInterfaceLanguage,
            false
        );

        $languageConfig->loadLanguageByLocale($interfaceLanguage);
        $this->view->lang = $languageConfig;
    }

    /**
     * Save recordesperPage- and interface language
     *
     * @param int    $recordsPerPage    Records per Page
     * @param string $interfaceLanguage Locale of the interface's language
     *
     * @return boolean
     */
    public function saveUserSettings($recordsPerPage, $interfaceLanguage)
    {
        $this->_dynamicConfig->setParam('recordsPerPage', $recordsPerPage);
        $res = $this->_userModel->saveSetting('recordsPerPage', $recordsPerPage);
        $res &= $this->_userModel->saveSetting('interfaceLanguage', $interfaceLanguage);
        return $res;
    }
}

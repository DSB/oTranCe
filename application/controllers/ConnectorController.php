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
 * Connector Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class ConnectorController extends Zend_Controller_Action
{
    /**
     * Will hold all keys to translate delivered by external call
     *
     * @var array
     */
    protected $_keys = array();

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

    public function init()
    {
        $this->_getKeys();
        $this->_entriesModel = new Application_Model_LanguageEntries();
        $this->_userModel    = new Application_Model_User();
        $this->view->user    = $this->_userModel;

        $this->_dynamicConfig  = Msd_Registry::getDynamicConfig();
        $this->_config         = Msd_Registry::getConfig();
        $this->_languagesModel = new Application_Model_Languages();
    }

    /**
     * Handle index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->setLanguages();
        $keys                          = array_keys($this->_keys);
        $hits                          = $this->_entriesModel->getIdsByKeys($keys);
        $this->view->hits              = $this->_entriesModel->assignTranslations(
            $this->view->showLanguages,
            $hits
        );
        $this->view->applicationValues = $this->_keys;
    }

    /**
     * Get keys from POST or session
     */
    protected function _getKeys()
    {
        $this->_keys = array();

        $ns = new Zend_Session_Namespace('requestData');
        if ($this->_request->isPost()) {
            // if keys are sent via post overwrite session value
            $keys = $this->_request->getParam('oTranceKeys', array());
            if (!empty($keys)) {
                $ns->data['oTranceKeys'] = $keys;
            }
        }

        if (!empty($ns->data['oTranceKeys'])) {
            $this->_keys = $ns->data['oTranceKeys'];
        }
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
        $this->_languagesEdit      = $this->_userModel->getUserLanguageRights();
        $this->view->languagesEdit = $this->_languagesEdit;

        // set reference languages
        $this->_referenceLanguages = $this->_userModel->getRefLanguages();
        $projectSettings           = $this->_config->getParam('project');
        if ($projectSettings['forceFallbackAsReference']) {
            // add main language as reference
            $this->_referenceLanguages = array_merge(
                array($this->_languagesModel->getFallbackLanguageId()),
                $this->_referenceLanguages
            );
            // in case user has set main language as reference - make each langId unique and show only once
            $this->_referenceLanguages = array_unique($this->_referenceLanguages);
        }
        $this->view->referenceLanguages = $this->_referenceLanguages;

        $this->_showLanguages      = $this->_languagesEdit;
        $this->_showLanguages      = array_merge($this->_showLanguages, $this->_referenceLanguages);
        $this->_showLanguages      = array_unique($this->_showLanguages);
        $this->view->showLanguages = $this->_showLanguages;
    }

}

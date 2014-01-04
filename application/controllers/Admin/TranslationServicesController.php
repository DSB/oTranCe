<?php
require_once 'AdminController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Vcs Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers_Admin
 */
class Admin_TranslationServicesController extends AdminController
{
    /**
     * Check general access right
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->checkRight('editTlServices');
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $translationServiceConf = $this->getTranslationServiceConfig();
        if ($this->_request->isPost()) {
            $translationServiceConf['selectedService'] = $this->_request->getParam('selectedService');
            $translationServiceConf['useService']      = $this->_request->getParam('useService');
            if ($this->_request->getParam('saveButton') !== null) {
                $this->_saveTranslationServiceConfig($translationServiceConf);
            }
        }

        $selectedTranslationService = $translationServiceConf['selectedService'];

        $this->_addAdapterLanguageFile($selectedTranslationService);
        $this->view->selectedService = $selectedTranslationService;
        $this->view->useService      = $translationServiceConf['useService'];

        $servicesBasePath              = realpath(APPLICATION_PATH . '/../modules/library/Translate/Service/');
        $this->view->availableServices = Msd_Translate::getAvailableTranslationServices($servicesBasePath);

        $this->view->adapterOptions = Msd_Translate::getInstance($selectedTranslationService)->getOptions();
    }

    /**
     * Add adapter specific language file
     *
     * @param string $adapterName Name of adapter
     *
     * @return void
     */
    protected function _addAdapterLanguageFile($adapterName)
    {
        Msd_Language::getInstance()->addTranslationFile(APPLICATION_PATH . '/language', $adapterName . '.php');
    }

    /**
     * Saves the config for translation services to config.ini.
     *
     * @param array $translationServiceConf Configuration to save
     *
     * @return void
     */
    private function _saveTranslationServiceConfig($translationServiceConf)
    {
        $this->_config->setParam('translationService', $translationServiceConf);
        $this->view->saved = $this->_config->save();
    }

    /**
     * Get standard config params from config.ini.
     *
     * If they don't exist set default values in case params are not present in config.ini.
     *
     * @return array
     */
    public function getTranslationServiceConfig()
    {
        $config                    = $this->_config->getParam('translationService', array());
        $config['selectedService'] = isset($config['selectedService']) ? $config['selectedService'] : 'MyMemory';
        $config['useService']      = isset($config['useService']) ? $config['useService'] : '0';

        return $config;
    }

    protected function _getAdapterOptions($selectedTranslationService)
    {

    }
}

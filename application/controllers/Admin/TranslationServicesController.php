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
     * The translation service instance
     *
     * @var Module_Translate_Service_Abstract
     */
    protected $_translationService;

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
        $translationServiceConf = $this->getGeneralConfig();

        if ($this->_request->isPost()) {
            $translationServiceName = $this->_request->getParam('selectedService');
            $useService             = $this->_request->getParam('useService');

            $translationService = $this->_getTranslationService($translationServiceName, true);
            $localeMap          = $this->_request->getParam('localeMap');
            foreach ($localeMap as $source => $target) {
                if ($target == '') {
                    unset($localeMap[$source]);
                }
            }
            $translationService->setLocaleMap($localeMap);

            $settings              = $this->_request->getParam($translationServiceName);
            $settings['localeMap'] = $localeMap;
            if ($this->_request->getParam('saveButton') !== null
                || $translationServiceConf['selectedService'] != $translationServiceName
            ) {
                $saved                  = $this->_saveGeneralConfig($translationServiceName, $useService);
                $saved                  = $saved && $translationService->saveSettings($settings);
                $translationServiceConf = $this->getGeneralConfig();
                $this->view->saved      = $saved;
            } else {
                // Did the user click the button "update locales"?
                if ($this->_request->getParam('updateLocales') !== null) {
                    $settings['serviceLocales'] = $translationService->getTranslatableLocales();
                    if (false === $settings['serviceLocales']) {
                        $this->view->updateLocales = false;
                    } else {
                        $this->view->updateLocales = $translationService->saveSettings($settings);
                    }
                }
                // Did the user click the button "auto map locales"?
                if ($this->_request->getParam('mapLocales') !== null) {
                    $this->_autoMapLocales($translationServiceName);
                }
            }
        }

        // get current translation service and load config values
        $translationServiceName = $translationServiceConf['selectedService'];

        // if auto map was executed we re-use the service instance because the map is not saved yet.
        if (!isset($translationService)) {
            $translationService = $this->_getTranslationService($translationServiceName, true);
        }

        // load additional language file for selected service if it exists
        $this->_addAdapterLanguageFile($translationServiceName);
        $this->view->selectedService = $translationServiceName;
        $this->view->useService      = $translationServiceConf['useService'];

        // get list of available translation services and tell view
        $servicesBasePath              = realpath(APPLICATION_PATH . '/../modules/library/Translate/Service/');
        $this->view->availableServices = Msd_Translate::getAvailableTranslationServices($servicesBasePath);
        $this->view->adapterOptions    = $translationService->getOptions();
        $this->view->serviceLocales    = $translationService->getLocales();
        $this->view->localeMap         = $translationService->getLocaleMap();
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
     * Saves input of options for selected translation service
     *
     * @param array $translationServiceName Configuration to save
     * @param bool  $useService             Whether the service is activated or not.
     *
     * @return bool
     */
    private function _saveGeneralConfig($translationServiceName, $useService)
    {
        $params = array(
            'selectedService' => $translationServiceName,
            'useService'      => $useService,
        );
        $this->_config->setParam('translationService', $params);

        return $this->_config->save();
    }

    /**
     * Get general config about selected translation service
     *
     * If they don't exist set default values in case params are not present in config.ini.
     *
     * @return array
     */
    public function getGeneralConfig()
    {
        $config                    = $this->_config->getParam('translationService', array());
        $config['selectedService'] = isset($config['selectedService']) ? $config['selectedService'] : 'MyMemory';
        $config['useService']      = isset($config['useService']) ? $config['useService'] : '0';

        return $config;
    }

    /**
     * Get and save list of available locales of the external translation service.
     * Boolean result is saved to view var updateLocales.
     *
     * @param string $translationServiceName Name of selcted translation service adapter
     *
     * @return array
     */
    protected function _loadLocalesFromExternalService($translationServiceName)
    {
        $translationService = $this->_getTranslationService($translationServiceName);

        return $translationService->getTranslatableLocales();
    }

    /**
     * Try to auto map locales of OTC rto locales of service provider
     *
     * @param string $translationServiceName
     *
     * @return void
     */
    protected function _autoMapLocales($translationServiceName)
    {
        $translationService = $this->_getTranslationService($translationServiceName);
        // get locale map of otc
        $otcLocales = array();
        foreach ($this->view->languages as $language) {
            $otcLocales[] = $language['locale'];
        }
        $translationService->autoMapLocales($otcLocales);
    }

    /**
     * Get instance of the given translation service
     *
     * @param string $translationServiceName Name of translation service
     * @param bool   $forceLoading           Whether to force new creation of instance
     *
     * @return Module_Translate_Service_Abstract
     */
    protected function _getTranslationService($translationServiceName, $forceLoading = false)
    {
        if ($this->_translationService === null || $forceLoading) {
            $this->_translationService = Msd_Translate::getInstance($translationServiceName);
        }

        return $this->_translationService;
    }
}

<?php
require_once 'AdminController.php';
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Admin/Export Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class Admin_ExportController extends AdminController
{
    public function indexAction()
    {
        $config = Msd_Configuration::getInstance();
        $vcsConf = $config->get('config.vcs');
        if ($this->_request->isPost()) {
            if ($this->_request->getParam('saveButton') !== null) {
                $this->_saveSvnConfig($config);
                $vcsConf = $config->get('config.vcs');
            } else {
                $vcsConf['adapter'] = $this->_request->getParam('vcsAdapter');
            }
        }
        $this->view->vcsAdapterParams = Msd_Vcs::getAdapterOptions($vcsConf['adapter']);
        $this->view->vcsConfig = $vcsConf;
        $this->view->vcsAvailAdapter = Msd_Vcs::getAvailableAdapter();

        $langModel = new Application_Model_Languages();
        $this->view->languages = $langModel->getAllLanguages();
        $this->view->fallbackLang = $langModel->getFallbackLanguage();
    }

    private function _saveSvnConfig(Msd_Configuration $config)
    {
        $subversionConf = array(
            'adapter' => $this->_request->getParam('vcsAdapter'),
            'commitMessage' => $this->_request->getParam('vcsCommitMessage'),
        );
        $config->set('config.vcs', $subversionConf);
        $config->set('config.vcs.options', $this->_request->getParam('vcsOptions'));

        $langModel = new Application_Model_Languages();
        $langModel->setFallbackLanguage($this->_request->getParam('fallbackLang'));

        $config->saveConfigToSession();
        $config->loadConfigFromSession();
        $this->_saveConfigToFile($config);
    }

    private function _saveConfigToFile(Msd_Configuration $config)
    {
        $configArray = $config->get('config');
        $newConfig = '';
        foreach ($configArray as $section => $sectionArray) {
            $newConfig .= "[$section]\n";
            foreach ($sectionArray as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $newConfig .= "$key.$subKey = $subValue\n";
                    }
                } else {
                    $newConfig .= "$key = $value\n";
                }
            }
        }
        $configFile = implode(
            DS,
            array(APPLICATION_PATH, 'configs', $config->get('dynamic.configFile') . '.ini')
        );
        file_put_contents($configFile, $newConfig);
    }
}

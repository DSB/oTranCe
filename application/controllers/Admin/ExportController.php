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
        if ($this->_request->isPost()) {
            $this->_saveSvnConfig($config);
        }
        $subversionConf = $config->get('config.subversion');
        $this->view->svnUser = $subversionConf['user'];
        $this->view->svnPass = $subversionConf['password'];
        $this->view->svnCommitOne = $subversionConf['commitMessageOneLanguage'];
        $this->view->svnCommitAll = $subversionConf['commitMessageAllLanguages'];
        $langModel = new Application_Model_Languages();
        $this->view->languages = $langModel->getAllLanguages();
        $this->view->fallbackLang = $langModel->getFallbackLanguage();
    }

    private function _saveSvnConfig(Msd_Configuration $config)
    {
        $subversionConf = array(
            'user' => $this->_request->getParam('svnUser'),
            'password' => $this->_request->getParam('svnPass'),
            'commitMessageOneLanguage' => $this->_request->getParam('svnCommitOne'),
            'commitMessageAllLanguages' => $this->_request->getParam('svnCommitAll'),
        );
        $config->set('config.subversion', $subversionConf);

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
                $newConfig .= "$key = $value\n";
            }
        }
        $configFile = implode(
            DS,
            array(APPLICATION_PATH, 'configs', $config->get('dynamic.configFile') . '.ini')
        );
        file_put_contents($configFile, $newConfig);
    }
}

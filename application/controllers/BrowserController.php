<?php
class BrowserController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $langModel     = new Application_Model_Languages();
        $templateModel = new Application_Model_FileTemplates();
        $langLocale = $this->_request->getParam('browseLang', '');
        $templateId = $this->_request->getParam('browseTemplate', 0);
        if ($this->_request->isPost()) {
            $template = $templateModel->getFileTemplate($templateId);
            $baseFilename = EXPORT_PATH . DS . $template['filename'];
            $filename = str_replace('{LOCALE}', $langLocale, $baseFilename);
            $this->view->filename = ltrim(str_replace(EXPORT_PATH, '', $filename), '/');
            $this->view->fileContent = "File doesn't exists, please run export first.";
            if (file_exists($filename)) {
                $this->view->fileContent = file($filename);
            }
        }
        $this->view->languages = $langModel->getAllLanguages('', 0, 0, true);
        $this->view->templates = $templateModel->getFileTemplates();
        $this->view->browseLang = $langLocale;
        $this->view->browseTemplate = $templateId;
    }
}

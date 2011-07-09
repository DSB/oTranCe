<?php
require_once('AdminController.php');
class Admin_ImportController extends AdminController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->_forward('analyze');
            return;
        }
    }

    public function analyzeAction()
    {
        $data = file('../data/lang.php');
        $importer = new Application_Model_Importer_Oxid();
        $this->view->extractedData = $importer->extract($data);
    }
}

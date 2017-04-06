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
 * REST Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class RestController extends OtranceController
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
     * @var Application_Model_FileTemplates
     */
    private $_fileTemplatesModel;

    /**
     * Ass. languages array which the user is allowed to edit
     *
     * @var array
     */
    private $_languages;

    /**
     * Check general access right and authenticate
     *
     * @return bool|void
     */
    public function preDispatch()
    {
        try {
            $this->checkRight('showImport');
        } catch (Msd_Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Print error message from Exception and set error code
     * @param Exception $e
     */
    protected function handleException(Exception $e)
    {
        $this->getRequest()->setDispatched(true);
        $this->getResponse()->clearBody();
        $jsonData = Zend_Json::encode(array(
            'error' => $e->getMessage(),
            'success' => false,
            'trace' => $e->getTrace()
        ));
        $code =  $e->getCode();
        $this->getResponse()
            ->setHttpResponseCode($code)
            ->setBody($jsonData)
            ->setHeader('Content-Type', 'application/json');
        $this->getResponse()->sendResponse();
        exit;
    }

    /**
     * Checks if the authenticated user has permissions for given right.
     *
     * @param string $right Name of right to check
     *
     * @return bool
     * @throws \Msd_Exception
     */
    public function checkRight($right)
    {
        if (!$this->_userModel->hasRight($right)) {
            /** @noinspection ClassConstantCanBeUsedInspection */
            throw new Msd_Exception('Resource forbidden for user', 403);
        }

        return true;
    }

    /**
     * Login user
     *
     * @param string $user
     * @param string $password
     * @return bool
     * @throws \Zend_Session_Exception
     * @throws \Msd_Exception
     * @throws \Zend_Form_Exception
     */
    protected function authenticateUser($user, $password)
    {
        $userModel = new Msd_User();
        $form = new Application_Form_Login();
        $historyModel = new Application_Model_History();
        $data = array('user' => $user, 'pass' => $password);
        $autoLogin = 0;

        if ($form->isValid($data)) {
            $loginResult = $userModel->login(
                $data['user'],
                $data['pass'],
                $autoLogin
            );

            $message = $userModel->getAuthMessages();
            if ($loginResult === Msd_User::SUCCESS) {
                $historyModel->logLoginSuccess();
                $this->_userModel = new Application_Model_User();
                return true;
            }

            if ($loginResult === Msd_User::NOT_ACTIVE) {
                $message = 'L_LOGIN_ACCOUNT_NOT_ACTIVE';
                $historyModel->logLoginFailed($data['user']);
            }

            throw new Msd_Exception(implode(',', $message), 401);
        }

        throw new Msd_Exception('Invalid authentication data', 401);
    }

    /**
     * Init
     *
     * @return void
     * @throws \Zend_Controller_Request_Exception
     */
    public function init()
    {
        /** @var  Zend_Controller_Request_Http $request */
        $request = $this->getRequest();
        try {
            $this->authenticateUser(
                $request->getHeader('userName'),
                $request->getHeader('userPass')
            );

            $this->setProjectFromHeader();

        } catch (Zend_Form_Exception $e) {
            $this->handleException(new Msd_Exception($e->getMessage(), 401));
        } catch (Zend_Session_Exception $e) {
            $this->handleException(new Msd_Exception($e->getMessage(), 401));
        } catch (Zend_Controller_Request_Exception $e) {
            $this->handleException(new Msd_Exception($e->getMessage(), 500));
        } catch (Msd_Exception $e) {
            $this->handleException($e);
        }

        $this->_entriesModel       = new Application_Model_LanguageEntries();
        $this->_languagesModel     = new Application_Model_Languages();
        $this->_fileTemplatesModel = new Application_Model_FileTemplates();
        // build array containing those languages the user is allowed to edit
        $allLanguages  = $this->_languagesModel->getAllLanguages();
        $userLanguages = $this->_userModel->getUserLanguageRights();
        if (!empty($userLanguages)) {
            $userLanguages = array_flip($userLanguages);
        }

        $this->_languages = array_uintersect_assoc(
            $allLanguages, $userLanguages,
            create_function(null, 'return 0;')
        );
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Set project in session from request header
     *
     * @throws Msd_Exception
     * @return void
     * @throws \Zend_Controller_Request_Exception
     */
    protected function setProjectFromHeader()
    {
        /** @var  Zend_Controller_Request_Http $request */
        $request = $this->getRequest();
        if ($request->getHeader('activeProject') === false) {
            throw new Msd_Exception('No project context provided', 400);
        }

        try {
            $project = $request->getHeader('activeProject');

            $this->_activeProject = new Application_Model_Project();
            $this->_dynamicConfig->setParam('activeProject', $project);
            $this->_dynamicConfig->setParam(
                'activeProjectId',
                $this->_activeProject->getProjectId($project)
            );

        } catch (Msd_Exception $e) {
            throw new Msd_Exception($e->getMessage(), 400);
        }
    }

    /**
     * Simple and one do it all REST action
     */
    public function translationsAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->handlePostTranslations();
        }

        if ($this->getRequest()->isGet()) {
            $this->handleGetTranslations();
        }
    }

    /**
     * Return list of translations
     */
    protected function handleGetTranslations()
    {
        $locale = $this->getRequest()->getParam('locale');
        if ($locale === null) {
            // lang local: de, en, es, etc...
            $this->handleException(new Msd_Exception(
                'No language locale (de, en, es, etc...) provided.', 400)
            );
        }

        $languages = $this->_languagesModel->getAllLanguages($locale);
        if (empty($languages)) {
            $this->handleException(new Msd_Exception(
                    'No language found for locale: ' . $locale, 400)
            );
        }

        $language = array_shift($languages);
        $keys = $this->_entriesModel->getAllKeysWithTranslations($language['id']);

        if (empty($keys)) {
            $this->handleException(new Msd_Exception(
                    'No translations found for locale: ' . $locale, 400)
            );
        }

        $this->handleResponse($keys, 200);
    }

    /**
     * Update list of translations
     */
    protected function handlePostTranslations()
    {
        $locale = $this->getRequest()->getParam('locale');
        if ($locale === null) {
            // lang local: de, en, es, etc...
            $this->handleException(new Msd_Exception(
                    'No language locale (de, en, es, etc...) provided.', 400)
            );
        }

        $translations = $this->getRequest()->getParam('translations');
        if ($translations=== null) {
            $this->handleException(new Msd_Exception(
                    'No translations provided. Doing nothing.', 400)
            );
        }

        $languages = $this->_languagesModel->getAllLanguages($locale);
        if (empty($languages)) {
            $this->handleException(new Msd_Exception(
                    'No language found for locale: ' . $locale, 400)
            );
        }

        $language = array_shift($languages);
        /*
         * translations should contain key => translated array entries
         */
        /** @var array $translations */
        $translations = Zend_Json::decode($translations);

        $keys = array_keys($translations);
        $keysIds = $this->_entriesModel->getIdsByKeys($keys);
        foreach ($keysIds as $key) {
            $this->_entriesModel->saveEntries(
                $key['id'],
                array($language['id'] => $translations[$key['key']])
            );
            unset($translations[$key['key']]);
        }

        $template_id = $this->_config->getParam('rest')['defaultTemplateId'];
        foreach ($translations as $key => $translation) {

            if (!$this->_entriesModel->saveNewKey($key, $template_id)) {
                $this->handleException(
                    new Msd_Exception('Could not sabe new key: ' . $key, 500)
                );
            }

            $lastInserted = $this->_entriesModel->getEntryByKey($key, $template_id);
            $this->_entriesModel->saveEntries(
                $lastInserted['id'],
                array($language['id'] => $translation)
            );
        }

        $this->handleResponse(array('message' => 'Successfully updated.'), 200);

    }

    protected function handleResponse(array $response, $code)
    {
        $this->getRequest()->setDispatched(true);
        $this->getResponse()->clearBody();
        $jsonData = Zend_Json::encode(array(
            'error' => '',
            'success' => true,
            'trace' => '',
            'data' => $response
        ));
        $this->getResponse()
            ->setHttpResponseCode($code)
            ->setBody($jsonData)
            ->setHeader('Content-Type', 'application/json');
        $this->getResponse()->sendResponse();
        exit;
    }
}

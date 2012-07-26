<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Models
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * Mail model
 * Encapsules all mail actions
 *
 * @package         oTranCe
 * @subpackage      Models
 */

class Application_Model_Mail extends Msd_Application_Model
{
    /**
     * @var \Zend_View_Interface Used for rendering texts
     */
    private $_view;

    /**
     * @var array Holds the project's configuration
     */
    public $projectConfig;

    /**
     * @var int Id of the currently selected language
     */
    protected $_originalLanguageLocale;

    /**
     * Init
     *
     * @param Zend_View_Interface $view Instance of view
     */
    public function __construct(\Zend_View_Interface $view)
    {
        parent::__construct();

        $this->_view = $view;

        // for debugging - write mail-text to a local file instead of sending
        // The file is created in system's temp dir.
        if (APPLICATION_ENV === 'development') {
            $mailPath = sys_get_temp_dir() . '/otc/';
            if (!file_exists($mailPath)) {
                mkdir($mailPath, 0777, true);
            }
            $transport = new Zend_Mail_Transport_File(
                array('path' => $mailPath)
            );
            Zend_Mail::setDefaultTransport($transport);
        }
        $this->projectConfig = $this->_config->getParam('project');
        $this->_setOriginalLanguage();
    }

    /**
     * Set view for rendering texts
     *
     * @param \Zend_View_Interface $view View instance
     *
     * @return void
     */
    public function setView(\Zend_View_Interface $view)
    {
        $this->_view = $view;
    }

    /**
     * Set the id of the currently selected language
     *
     * @return void
     */
    private function _setOriginalLanguage()
    {
        $this->_originalLanguageLocale = $this->_view->lang->getActiveLanguage();
    }

    /**
     * (Re-)Load the formerly set selected language
     *
     * @return void
     */
    private function _loadOriginalLanguage()
    {
        $this->_view->lang->loadLanguageByLocale($this->_originalLanguageLocale);
    }

    /**
     * Load fall back language
     *
     * @return void
     */
    private function _loadFallbackLanguage()
    {
        $languageModel          = new Application_Model_Languages();
        $fallbackLanguageId     = $languageModel->getFallbackLanguageId();
        $fallbackLanguageLocale = $languageModel->getLanguageLocaleFromId($fallbackLanguageId);
        if ($fallbackLanguageLocale === false) {
            $fallbackLanguageLocale = 'en';
        }
        $this->_view->lang->loadLanguageByLocale($fallbackLanguageLocale);
    }

    /**
     * Sends info-mail about a registered user to admin
     *
     * @param array $userData          Array containing the users data
     * @param array $languagesMetaData Languages meta data
     *
     * @throws Exception
     *
     * @return void
     */
    public function sendAdminRegisterInfoMail($userData, $languagesMetaData)
    {
        /**
         * @var Zend_Translate_Adapter $translator
         */
        if (!isset($this->projectConfig['email']) || trim($this->projectConfig['email']) == '') {
            // no project contact e-mail set -> can't send mail
            return;
        }

        $this->_view->assign(
            array(
                'user'      => $userData,
                'project'   => $this->projectConfig,
                'languages' => $languagesMetaData,
            )
        );

        $this->_setOriginalLanguage();
        // we inform the administrator in the fallback language
        $this->_loadFallbackLanguage();

        $htmlBody      = $this->_view->render('mail/admin-register-info.phtml');
        $plainTextBody = $this->_view->render('mail/admin-register-info-plain.phtml');

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($htmlBody)
            ->setBodyText($plainTextBody)
            ->setFrom($this->projectConfig['email'], $this->projectConfig['name'])
            ->addTo($this->projectConfig['email'], $this->projectConfig['name'])
            ->setReplyTo($userData['email'], $userData['realName']);
        $translator = $this->_view->lang->getTranslator();
        $subject    = $translator->translate('L_REGISTER_MAIL_SUBJECT');
        $mail->setSubject(sprintf($subject, $this->projectConfig['name'], $userData['username']));
        $mail->send();
        $this->_loadOriginalLanguage();
    }

    /**
     * Sends info-mail about account activating to user
     *
     * @param array $userData Array containing the users data
     *
     * @throws Exception
     *
     * @return void
     */
    public function sendAccountActivationInfoMail($userData)
    {
        if (!isset($this->projectConfig['email']) || trim($this->projectConfig['email']) == ''
            || trim($userData['email']) == ''
        ) {
            return;
        }

        $this->_view->assign(
            array(
                'userData'  => $userData,
                'project'   => $this->projectConfig,
            )
        );
        $mail = $this->_getUserMail($userData, 'mail/user-account-activated', 'L_ACCOUNT_ACTIVATED_SUBJECT');
        $mail->send();
    }

    /**
     * Create mail object that will be sent to the user and set body text, subject, user language and recipient data.
     * Will restore the current language for further actions.
     *
     * @param array  $userData     Data of user containing username, name and e-mail address
     * @param string $mailTemplate File name of template to render
     * @param string $subject      Language var used for composing the mail's subject line
     *
     * @return Zend_Mail
     */
    protected function _getUserMail($userData, $mailTemplate, $subject)
    {
        $this->_setOriginalLanguage();
        $this->_assignUserLanguage($userData['id']);

        $htmlBody      = $this->_view->render($mailTemplate . '.phtml');
        $plainTextBody = $this->_view->render($mailTemplate . '-plain.phtml');

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($htmlBody)
            ->setBodyText($plainTextBody)
            ->setFrom($this->projectConfig['email'], $this->projectConfig['name'])
            ->setReplyTo($this->projectConfig['email'], $this->projectConfig['name'])
            ->addTo($userData['email'], $userData['realName']);

        $translator = $this->_view->lang->getTranslator();
        $subject    = $translator->translate($subject);
        $mail->setSubject(sprintf($subject, $userData['username'], $this->projectConfig['name']));
        $this->_loadOriginalLanguage();

        return $mail;
    }

    /**
     * Get the user setting "interfaceLanguage" and assign it to the template.
     *
     * @param int $userId Id of user
     */
    protected function _assignUserLanguage($userId)
    {
        $this->_setOriginalLanguage();
        // load language of user
        $userModel          = new Application_Model_User();
        $userLanguageLocale = $userModel->getUserLanguageLocale($userId);
        $this->_view->lang->loadLanguageByLocale($userLanguageLocale);
    }

}

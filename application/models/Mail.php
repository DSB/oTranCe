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
    public function __construct(Zend_View_Interface $view)
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
    public function setView(Zend_View_Interface $view)
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
     * @return void
     */
    public function sendUserRegisteredMail($userData, $languagesMetaData)
    {
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

        $subjectArgs = array($this->projectConfig['name'], $userData['username']);
        $mail = $this->_getAdminMail($userData, 'admin/register', 'L_REGISTER_MAIL_SUBJECT', $subjectArgs);
        $mail->send();
    }

    /**
     * Sends info-mail about requested language edit right of a registered user to admin
     *
     * @param array $userData     Array containing the users data
     * @param array $languageData Languages meta data
     *
     * @return void
     */
    public function sendEditRightRequestedMail($userData, $languageData)
    {
        if (!isset($this->projectConfig['email']) || trim($this->projectConfig['email']) == '') {
            // no project contact e-mail set -> can't send mail
            return;
        }

        $this->_view->assign(
            array(
                'userData' => $userData,
                'language' => $languageData,
            )
        );

        $subjectArgs = array($userData['username'], $languageData['name'], $languageData['locale']);
        $mail = $this->_getAdminMail($userData, 'admin/edit-right-requested', 'L_EDIT_RIGHT_REQUESTED', $subjectArgs);
        $mail->send();
    }

    /**
     * Sends info-mail about account activating to user
     *
     * @param array $userData Array containing the users data
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

        $subjectArgs = array($userData['username'], $this->projectConfig['name']);
        $this->_view->assign(array('userData'  => $userData, 'project'   => $this->projectConfig));
        $mail = $this->_getUserMail($userData, 'user/account-activated', 'L_ACCOUNT_ACTIVATED_SUBJECT', $subjectArgs);
        $mail->send();
    }

    /**
     * Sends info-mail about assignign a new edit right of a language to the user.
     *
     * @param array $userData     Array containing the users data
     * @param array $languageData Array containing the indexes 'name' and 'locale'
     *
     * @return void
     */
    public function sendEditRightGrantedMail($userData, $languageData)
    {
        if (!isset($this->projectConfig['email']) || trim($this->projectConfig['email']) == ''
            || trim($userData['email']) == ''
        ) {
            return;
        }
        $subjectArgs = array($languageData['name']);
        $this->_view->assign(array('userData' => $userData, 'languageData' => $languageData));
        $mail = $this->_getUserMail($userData, 'user/edit-right-granted', 'L_EDIT_RIGHT_ADDED_TO', $subjectArgs);
        $mail->send();
    }

    /**
     * Create mail object that will be sent to the user and set body text, subject, user language and recipient data.
     * Will restore the current language for further actions.
     *
     * @param array  $userData     Data of user containing username, name and e-mail address
     * @param string $mailTemplate File name of template to render
     * @param string $subject      Language var used for composing the mail's subject line
     * @param array  $subjectArgs  Values of placeholders in subject line
     *
     * @return Zend_Mail
     */
    protected function _getUserMail($userData, $mailTemplate, $subject, $subjectArgs = array())
    {
        $this->_setOriginalLanguage();
        $this->_assignUserLanguage($userData['id']);

        $translator    = $this->_view->lang->getTranslator();
        $greetLine     = sprintf($translator->translate('L_EMAIL_HEADER'), $userData['username']);
        $footer        = sprintf($translator->translate('L_EMAIL_FOOTER'), $this->projectConfig['name']);
        $htmlBody      = $greetLine . '<br /><br />' . $this->_view->render('mail/' . $mailTemplate . '.phtml')
            . '<br /><br />' . $footer;
        $plainTextBody = $greetLine . "\n\n" . $this->_view->render('mail/' . $mailTemplate . '-plain.phtml')
            . "\n\n" . $footer;

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($htmlBody)
            ->setBodyText($plainTextBody)
            ->setFrom($this->projectConfig['email'], $this->projectConfig['name'])
            ->setReplyTo($this->projectConfig['email'], $this->projectConfig['name'])
            ->addTo($userData['email'], $userData['realName']);
        $subject = $translator->translate($subject);
        // replace placeholder with values if given
        if (!empty($subjectArgs)) {
            $subjectLine = vsprintf($subject, $subjectArgs);
        }
        $mail->setSubject($subjectLine);
        $this->_loadOriginalLanguage();

        return $mail;
    }

    /**
     * Create mail object that will be sent to the administrator and set body text, subject, user language
     * and recipient data.
     *
     * Will restore the current language for further actions.
     *
     * @param array  $userData     Data of user
     * @param string $mailTemplate File name of template to render
     * @param string $subject      Language var used for composing the mail's subject line
     * @param array  $subjectArgs  Values of placeholders in subject line
     *
     * @return Zend_Mail
     */
    protected function _getAdminMail($userData, $mailTemplate, $subject, $subjectArgs = array())
    {
        $this->_setOriginalLanguage();
        $this->_loadFallbackLanguage();

        $translator    = $this->_view->lang->getTranslator();
        $greetLine     = sprintf($translator->translate('L_EMAIL_HEADER'), $translator->translate('L_ADMIN'));
        $footer        = sprintf($translator->translate('L_EMAIL_FOOTER'), $this->projectConfig['name']);
        $htmlBody      = $greetLine . '<br /><br />' . $this->_view->render('mail/' . $mailTemplate . '.phtml')
            . '<br /><br />' . $footer;
        $plainTextBody = $greetLine . "\n\n" . $this->_view->render('mail/' . $mailTemplate . '-plain.phtml')
            . "\n\n" . $footer;

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($htmlBody)
            ->setBodyText($plainTextBody)
            ->setFrom($this->projectConfig['email'], $this->projectConfig['name'])
            ->setReplyTo($userData['email'], $userData['username'])
            ->addTo($this->projectConfig['email'], $this->projectConfig['name']);
        $subject = $translator->translate($subject);
        // replace placeholder with values if given
        if (!empty($subjectArgs)) {
            $subjectLine = vsprintf($subject, $subjectArgs);
        }
        $mail->setSubject($subjectLine);
        $this->_loadOriginalLanguage();

        return $mail;
    }

    /**
     * Get the user setting "interfaceLanguage" and assign it to the template.
     *
     * @param int $userId Id of user
     *
     * @return void
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

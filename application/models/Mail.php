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
     * Init
     *
     * @param Zend_View_Interface $view Instance of view
     */
    public function __construct(\Zend_View_Interface $view = null)
    {
        parent::__construct();
        if ($view !== null) {
            $this->_view = $view;
        }
        // for debugging - write mail-text to a local file instead of sending
        if (APPLICATION_ENV === 'development') {
            $transport = new Zend_Mail_Transport_File(
                array('path' => APPLICATION_PATH . '/../data/')
            );
            Zend_Mail::setDefaultTransport($transport);
        }
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
        $projectConfig = $this->_config->getParam('project');
        if (trim($projectConfig['email'] == '')) {
            // no project contact e-mail set -> can't send mail
            return;
        }
        if ($this->_view === null) {
            throw new Exception('No view for rendering set: call setView($view) first.');
        }

        $this->_view->assign(
            array(
                 'user'      => $userData,
                 'project'   => $projectConfig,
                 'languages' => $languagesMetaData,
            )
        );
        $htmlBody      = $this->_view->render('mail/admin-register-info.phtml');
        $plainTextBody = $this->_view->render('mail/admin-register-info-plain.phtml');

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($htmlBody)
            ->setBodyText($plainTextBody)
            ->setFrom($projectConfig['email'], $projectConfig['name'])
            ->addTo($userData['email'], $userData['realName']);
        $translator = $this->_view->lang->getTranslator();
        $subject    = $translator->translate('L_REGISTER_MAIL_SUBJECT');
        $mail->setSubject(sprintf($subject, $projectConfig['name'], $userData['username']));
        $mail->send();
    }
}

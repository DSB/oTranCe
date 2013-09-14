<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://www.otrance.org
 *
 * @package         oTranCe
 * @subpackage      Login
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Create login form
 *
 * @package         oTranCe
 * @subpackage      Login
 */
class Application_Form_ForgotPassword extends Zend_Form
{
    /**
     * Init form and add all elements
     *
     * @return void
     */
    public function init()
    {
        $translator = Msd_Language::getInstance()->getTranslator();

        $userEmail = $this->createElement('text', 'user_email', array('Label' => 'Bladf'));

        $this->addElement(
            $userEmail
        );

    }
}

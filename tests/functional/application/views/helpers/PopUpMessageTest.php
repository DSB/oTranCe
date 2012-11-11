<?php
/**
 * @group MsdViewHelper
 */
class PopUpMessageTest extends ControllerTestCase
{
    public function testcanAddPopUpMessage()
    {
        // force popUp by log in with wrong credentials
        $this->getRequest()
              ->setMethod('POST')
              ->setParams(
                  array(
                        'user' => 'tester',
                        'pass' => 'wrongPassword',
                        'autologin' => 0
                  )
              );
        $this->dispatch('/index/login');
        $this->assertNotRedirect();
        // make sure we see the login error message
        $this->assertQueryCount("//div[@id='login-message']", 1);
        $view = Zend_Layout::getMvcInstance()->getView();
        $this->assertQueryContentContains('#login-message', $view->lang->translate('L_LOGIN_INVALID_USER'));
    }


    public function testInsertsMissingParams()
    {
        $this->view = Zend_Layout::getMvcInstance()->getView();
        $this->view->popUpMessage()->addMessage('testMessageId', 'test title', 'test message');
        $message = (string) $this->view->popUpMessage();
        $this->assertEquals('<div style="display:none" id="testMessageId">test message</div>', $message);
    }

}


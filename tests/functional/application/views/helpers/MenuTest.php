<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Menu.php';

/**
 * @group MsdViewHelper
 */
class MenuTest extends ControllerTestCase
{
    public function testWontRenderMenuAtLoginAction()
    {
        $this->dispatch('/index/login');
        $this->assertQueryCount('form', 1);
        $this->assertQueryCount('#user', 1);
        $this->assertQueryCount('#pass', 1);
        $this->assertQueryCount('#autologin', 1);
        $this->assertQueryCount('#send', 1);
    }

}


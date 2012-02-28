<?php
/**
 * @group Plugin
 */
class LoginCheckTest extends ControllerTestCase
{
    public function testRedirectsToLoginFormIfUserIsNotLoggedIn()
    {
        $this->dispatch('/');
        $this->assertResponseCode('302');
        $this->assertRedirectTo('http:///index/login/');
    }

    public function testDoesNotRedirectIfUserIsLoggedIn()
    {
        $this->loginUser();
        $this->dispatch('settings/index');
        $this->assertResponseCode('200');
        $this->assertNotRedirect();
        $this->assertController('settings');
        $this->assertAction('index');
    }
}

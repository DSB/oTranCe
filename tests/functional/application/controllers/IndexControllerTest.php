<?php
/**
 * @group Controllers
 */
class IndexControllerTest extends ControllerTestCase
{
    /**
     * @outputBuffering enabled
     */
    public function testLogoutAction()
    {
        $this->loginUser();
        $this->dispatch('/index/logout');
        $this->resetResponse();  // clear header from cookie
        // request a page which needs a log in
        $this->dispatch('/settings');
        // now we must be redirected to the log in page
        $this->assertRedirect('/index/login');
    }
}

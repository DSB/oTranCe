<?php
/**
 * @group Controllers
 */
class StatisticsControllerTest extends ControllerTestCase
{
    public function testIndexAction()
    {
        $this->loginUser();
        $this->dispatch('/statistics');
        $this->assertQueryContentContains('h2', 'Statistics');
        $this->assertQueryContentContains('th', 'Edit actions');
    }

    public function testIndexActionRedirectsIfUserIsNotAllowedToAccess()
    {
        $userModel = new Application_Model_User();
        $deletedRight = $userModel->saveRight(2, 'showStatistics', 0);
        $this->assertTrue($deletedRight);
        $this->loginUser();
        $this->dispatch('/statistics');
        $this->assertRedirect('/');
    }

}

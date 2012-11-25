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
        $this->assertQueryContentContains('h2', $this->_translator->_('L_STATISTICS'));
        $this->assertQueryContentContains('h4', $this->_translator->_('L_ACTIVITIES'));
        $this->assertQueryContentContains('th', $this->_translator->_('L_EDITED'));
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

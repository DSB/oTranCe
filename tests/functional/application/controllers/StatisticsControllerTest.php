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
}

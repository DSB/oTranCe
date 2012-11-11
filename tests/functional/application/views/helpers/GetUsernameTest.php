<?php
require_once 'GetUsername.php';
/**
 * @group MsdViewHelper
 */
class GetUsernameTest extends ControllerTestCase
{
    public function testCanGetUsername()
    {
        $this->loginUser();
        $viewHelper = new Msd_View_Helper_GetUsername();
        $res = strtolower($viewHelper->getUsername());
        $this->assertTrue(is_string($res));
        $this->assertEquals('tester', $res);
    }

}

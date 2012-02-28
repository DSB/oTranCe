<?php
/**
 * @group User
 */
class UserTest extends ControllerTestCase
{
    /**
     * @var \Application_Model_User
     */
    protected $userModel;

    public function setUp()
    {
        $this->userModel = new Application_Model_User();
    }

    public function testCanGetUserId()
    {
        $this->loginUser();
        $this->userModel = new Application_Model_User();
        $this->assertEquals(2, $this->userModel->getUserId());

        $this->clearRequest();
        $this->loginUser('Admin', 'admin');
        $this->userModel = new Application_Model_User();
        $this->assertEquals(1, $this->userModel->getUserId());
    }

    public function testCanGetTranslatorsOfALanguage()
    {
        $this->userModel = new Application_Model_User();
        $translators = $this->userModel->getTranslators(1);
        // we expect an array(languageId => array(userId1, userId2))
        // admin (1) and tester(2) do have edit rights for id 1 (Deutsch)
        $expected = array(1 => array(1,2));
        $this->assertEquals($expected, $translators);

        $translators = $this->userModel->getTranslators(2);
        // only admin (1) does have edit rights for id 2 (Englisch)
        $expected = array(2 => array(1));
        $this->assertEquals($expected, $translators);
    }

    public function testGetTranslatorsReturnsEmptyArrayForNonExistantLanguage()
    {
        $translators = $this->userModel->getTranslators(9999999);
        $this->assertEquals(array(), $translators);
    }

}
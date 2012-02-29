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

    public function testGetTranslatorsReturnsEmptyArrayForNonExistentLanguage()
    {
        $translators = $this->userModel->getTranslators(9999999);
        $this->assertEquals(array(), $translators);
    }

    public function testGetTranslatorListCanImplodeList()
    {
        $translatorList = $this->userModel->getTranslatorlist(true);
        $expected = array( 1=> 'Admin, tester', 2=> 'Admin');
        $this->assertEquals($expected, $translatorList);
    }

    public function testGetUsers()
    {
        // test filter
        $userList = $this->userModel->getUsers('Ad');
        $expected = array(1 => array(
                'id'       => 1,
                'username' => 'Admin',
                'password' => '21232f297a57a5a743894a0e4a801fc3',
                'active'   => 1
            )
        );
        $this->assertEquals($expected, $userList);

        // test pagination
        $userList = $this->userModel->getUsers('', 1, 1);
        $expected = array(2 => array(
                'id'       => '2',
                'username' => 'tester',
                'password' => '098f6bcd4621d373cade4e832627b4f6',
                'active'   => 1
            )
        );
        $this->assertEquals($expected, $userList);
    }

    public function testGetUserNames()
    {
        $userNames = $this->userModel->getUserNames();
        $expected = array( 1 => 'Admin', 2 => 'tester');
        $this->assertEquals($expected, $userNames);
    }

    public function testGetUserById()
    {
        //get id 1 = Admin
        $user = $this->userModel->getUserById(1);
        $expected = array(
            'id'       => 1,
            'username' => 'Admin',
            'password' => '21232f297a57a5a743894a0e4a801fc3',
            'active'   => 1
        );
        $this->assertEquals($expected, $user);

        // get id 2 = tester
        $user = $this->userModel->getUserById(2);
        $expected = array(
            'id'       => '2',
            'username' => 'tester',
            'password' => '098f6bcd4621d373cade4e832627b4f6',
            'active'   => 1
        );
        $this->assertEquals($expected, $user);

        // request invalid user and get default array
        $user = $this->userModel->getUserById(99999999);
        $expected = array(
            'id'       => '0',
            'username' => '',
            'password' => '',
            'active'   => 0
        );
        $this->assertEquals($expected, $user);
    }

    public function testGetUserByName()
    {
        $user = $this->userModel->getUserByName('Admin');
        $expected = array(
            'id'       => 1,
            'username' => 'Admin',
            'password' => '21232f297a57a5a743894a0e4a801fc3',
            'active'   => 1
        );
        $this->assertEquals($expected, $user);
    }

    public function testGetRowCount()
    {
        $users = $this->userModel->getUserNames();
        $rowCount = $this->userModel->getRowCount();
        $this->assertEquals(sizeof($users), $rowCount);
    }

    public function testLoadSetting()
    {
        $this->loginUser();
        $this->userModel = new Application_Model_User();
        $setting = $this->userModel->loadSetting('interfaceLanguage');
        $this->assertEquals('de', $setting);

        // test force returning as array
        $setting = $this->userModel->loadSetting('recordsPerPage', '', true);
        $this->assertEquals(array(0 => 10), $setting);
    }

    public function testGetRefLanguages()
    {
        $this->loginUser('Admin', 'admin');
        $this->userModel = new Application_Model_User();
        $referenceLanguages = $this->userModel->getRefLanguages();
        $this->assertEquals(array( 0 => 1), $referenceLanguages);
    }

   public function testGetReferenceLanguageStatus()
    {
        // check for set reference language
        $referenceLanguageStatus = $this->userModel->getReferenceLanguageStatus(1, 1);
        $this->assertEquals(1, $referenceLanguageStatus);

        // check for unset reference language
        $referenceLanguageStatus = $this->userModel->getReferenceLanguageStatus(1, 99999);
        $this->assertEquals(0, $referenceLanguageStatus);
    }

    /**
     * @depends testGetReferenceLanguageStatus
     */
    public function testSwitchReferenceLanguageStatus()
    {
        $this->userModel->switchReferenceLanguageStatus(1, 1);
        $referenceLanguageStatus = $this->userModel->getReferenceLanguageStatus(1, 1);
        $this->assertEquals(0, $referenceLanguageStatus);

        $this->userModel->switchReferenceLanguageStatus(1, 1);
        $referenceLanguageStatus = $this->userModel->getReferenceLanguageStatus(1, 1);
        $this->assertEquals(1, $referenceLanguageStatus);
    }

    public function testSaveSetting()
    {
        $this->loginUser('Admin', 'admin');
        $this->userModel = new Application_Model_User();
        $this->userModel->saveSetting('foo', 'bar');
        $setting = $this->userModel->loadSetting('foo');
        $this->assertEquals('bar', $setting);
    }

    /**
     * @depends testSaveSetting
     */
    public function testDeleteSetting()
    {
        $this->loginUser('Admin', 'admin');
        $this->userModel = new Application_Model_User();
        $this->userModel->deleteSetting('foo');
        $setting = $this->userModel->loadSetting('foo', 'notFound');
        $this->assertEquals('notFound', $setting);
    }

    public function testAddUsersEditLanguageRight()
    {
        $this->userModel->deleteUsersEditLanguageRight(1, 9999);
        $saved = $this->userModel->addUsersEditLanguageRight(1, 9999);
        $this->assertTrue($saved);
    }

    /**
     * @depends testAddUsersEditLanguageRight
     */
    public function testDeleteUsersEditLanguageRight()
    {
        $deleted = $this->userModel->deleteUsersEditLanguageRight(1, 9999);
        $this->assertTrue($deleted);
    }

}
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Darky
 * Date: 22.08.11
 * Time: 20:17
 * To change this template use File | Settings | File Templates.
 */
 
class ConfigTest extends PHPUnit\Framework\TestCase
{
    private $_savedConfig = null;
    /**
     * @return Msd_Config
     */
    private function _getMockedConfigObject()
    {
        $ioHandler = $this->createMock('Msd_Config_IoHandler_Default', array('load', 'save'));
        $ioHandler->expects($this->any())->method('load')->with($this->equalTo('phpunit.ini'))->will(
            $this->returnValue(array('foo' => 'bar'))
        );

        $ioHandler->expects($this->any())->method('save')->will(
            $this->returnCallback(array($this, '_saveConfigMock'))
        );
        return new Msd_Config($ioHandler);
    }

    public function _saveConfigMock($config)
    {
        $this->_savedConfig = $config;
    }

    /**
     * @expectedException Msd_Config_Exception
     */
    public function testMsdConfigThrowsAnExceptionIfTheIoHandlerIsInvalid()
    {
        $invalidIoHandler = new stdClass();
        new Msd_Config($invalidIoHandler);
    }

    public function testIfFirstParamIsAStringCreateAnIoHandlerWithItAsName()
    {
        $config = new Msd_Config('Default', array('directories' => APPLICATION_PATH . '/configs/'));
        $this->assertInstanceOf('Msd_Config', $config);
    }

    public function testConfigCanBeLoaded()
    {
        $config = $this->_getMockedConfigObject();
        $config->load('phpunit.ini');
    }

    public function testConfigCanBeSaved()
    {
        $config = $this->_getMockedConfigObject();
        $config->load('phpunit.ini');
        $config->save();
        $this->assertInternalType('array', $this->_savedConfig);
    }

    public function testConfigParameterCanBeRetrieved()
    {
        $config = $this->_getMockedConfigObject();
        $config->load('phpunit.ini');
        $this->assertEquals('bar', $config->getParam('foo'));
    }

    public function testConfigParameterCanBeSet()
    {
        $config = $this->_getMockedConfigObject();
        $config->load('phpunit.ini');
        $config->setParam('foo', 'baz');
        $this->assertEquals('baz', $config->getParam('foo'));
    }

    public function testGetParamReturnsDefaultValueIfSettingIsNotPresent()
    {
        $config = $this->_getMockedConfigObject();
        $config->load('phpunit.ini');
        $noConfig = $config->getParam('IDidNotExists');
        $this->assertNull($noConfig);

        $noConfig = $config->getParam('IDidNotExists', 'IAmADefaultValue');
        $this->assertEquals('IAmADefaultValue', $noConfig);
    }

    public function testCanRetrieveTheWholeConfigurationArray()
    {
        $config = $this->_getMockedConfigObject();
        $config->load('phpunit.ini');

        $wholeConfig = $config->getConfig();
        $this->assertNotEmpty($wholeConfig);
        $this->assertEquals('bar', $wholeConfig['foo']);
    }

    public function testCanSetTheWholeConfigurationArray()
    {
        $config = $this->_getMockedConfigObject();
        $config->load('phpunit.ini');

        $newConfig = array('bar' => 'baz');
        $config->setConfig($newConfig);
        $this->assertEquals('baz', $config->getParam('bar'));
    }

    public function testAutosaveIsDisabledByDefault()
    {
        $config = new Msd_Config('Default');
        $config->load('config.ini', array('directories' => APPLICATION_PATH . '/configs/'));

        $this->assertFalse($config->isAutosaveActive());
    }

    public function testCanActivateAutosaving()
    {
        Testhelper::copyFile('config.ini', APPLICATION_PATH . '/configs/phpunit_test.ini');
        $config = new Msd_Config('Default', array('directories' => APPLICATION_PATH . '/configs/'));
        $config->load('phpunit_test.ini');

        $config->enableAutosave();
        $this->assertTrue($config->isAutosaveActive());

        // trigger autosave
        $config->setConfig($config->getConfig());
    }

    /**
     * @depends testCanActivateAutosaving
     */
    public function testCanActivateAndDeactivateAutosaving()
    {
        $config = new Msd_Config('Default', array('directories' => APPLICATION_PATH . '/configs/'));
        $config->load('phpunit_test.ini');

        $config->enableAutosave();
        $this->assertTrue($config->isAutosaveActive());

        $config->setParam('foo', array('bar' => 'baz'));

        $config->disableAutosave();
        $this->assertFalse($config->isAutosaveActive());
    }
}

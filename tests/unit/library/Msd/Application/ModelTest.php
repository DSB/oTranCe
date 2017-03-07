<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Darky
 * Date: 22.08.11
 * Time: 22:34
 * To change this template use File | Settings | File Templates.
 */
 
class ModelTest extends PHPUnit\Framework\TestCase
{
    public function testCanCreateAnInstanceOfApplicationModel()
    {
        $appModel = $this->getMockForAbstractClass('Msd_Application_Model');
        $this->assertInstanceOf('Msd_Application_Model', $appModel);
    }
}

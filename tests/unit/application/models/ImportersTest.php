<?php
/**
 * @group Importers
 * @group Models
 */
class ImportersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Application_Model_Importers
     */
    protected $model;

    public function setUp()
    {
        $this->model = new Application_Model_Importers();
    }

    public function testCanGetImporters()
    {
        $importers = array_keys($this->model->getImporter());
        $expected  = array(
            0 => 'Csv',
            1 => 'Oxid',
            2 => 'PhpArray',
            3 => 'Redaxo',
            4 => 'Ssv',
            5 => 'Titanium',
        );
        $this->assertEquals($expected, $importers);
    }

    public function testCanActiveImporters()
    {
        $importers = array(
            'Csv'      => 1,
            'Oxid'     => 0,
            'PhpArray' => 1,
            'Redaxo'   => 0,
            'Ssv'      => 0,
            'Titanium' => 0,
        );
        // save importers
        $config    = Msd_Registry::getConfig();
        $config->setParam('importers', $importers);
        $config->save();

        // check we only get the active ones
        $importers = $this->model->getActiveImporters();
        $expected  = array(
            'Csv'      => 1,
            'PhpArray' => 1
        );
        $this->assertEquals($expected, $importers);
    }

}

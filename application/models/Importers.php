<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Models
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * Converter model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_Importers extends Msd_Application_Model
{
    /**
     * Get all available importers and their status (on/off) as ass. array.
     *
     * @return array
     */
    public function getImporter()
    {
        $importers          = $this->_config->getParam('importers', array());
        $availableImporters = Msd_Import::getAvailableImportAnalyzers();
        foreach ($availableImporters as $importerName) {
            if (!isset($importers[$importerName])) {
                // found an importer that is not saved to the config yet -> set default to off
                $importers[$importerName] = 0;
            }
        }
        ksort($importers, SORT_LOCALE_STRING);
        return $importers;
    }

    /**
     * Get active importers
     *
     * @return array
     */
    public function getActiveImporters()
    {
        $activeImporters = $this->getImporter();
        foreach ($activeImporters as $importerName => $status) {
            if ((int)$status !== 1) {
                unset($activeImporters[$importerName]);
            }
        }
        return $activeImporters;
    }

    /**
     * Set the given importer as standard importer.
     *
     * @param string $importerId Id of importer
     *
     * @return bool
     */
    public function setStandardImporter($importerId)
    {
        $userModel = new Application_Model_User();
        return $userModel->saveSetting('standardImporter', $importerId, 0);
    }

    /**
     * Get the id of the selected standard importer.
     *
     * @return string
     */
    public function getStandardImporter()
    {
        $userModel = new Application_Model_User();
        return $userModel->loadSetting('standardImporter', '', false, 0);
    }

}

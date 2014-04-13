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
 * MoudleConfig model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_ModuleConfig extends Msd_Application_Model
{
    /**
     * Database table containing translations.
     *
     * @var string
     */
    private $_tableModuleConfig;

    /**
     * Will hold module data
     *
     * @var array
     */
    protected $_moduleData = array();

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $tableConfig              = $this->_config->getParam('table');
        $this->_tableModuleConfig = $tableConfig['module_config'];
    }

    /**
     * Get settings of the given module
     *
     * @param string $moduleId     Get all saved settings of given module
     * @param bool   $forceLoading Force loading data from database
     *
     * @return array
     */
    public function getModuleSettings($moduleId, $forceLoading = false)
    {
        if (!$forceLoading && isset($this->_moduleData[$moduleId])) {
            return $this->_moduleData[$moduleId];
        }

        $sql = 'SELECT `varname` as varName, `varvalue` as varValue FROM `' . $this->_tableModuleConfig . '`'
            . ' WHERE `module_id` = "' . $this->_dbo->escape($moduleId) . '"';

        $res = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC, true);

        $settings = array();
        if (!empty($res)) {
            foreach ($res as $r) {
                // settings prefixed with @@@ are serialized arrays or objects
                if (substr($r['varValue'], 0, 3) === '@@@') {
                    $r['varValue'] = unserialize(substr($r['varValue'], 3));
                }
                $settings[$r['varName']] = $r['varValue'];
            }
        }

        $this->_moduleData[$moduleId] = $settings;

        return $this->_moduleData[$moduleId];
    }

    /**
     * Set a value of a module
     *
     * @param string $moduleId Id of module
     * @param string $varName  Name of variable to set
     * @param string $varValue Value
     *
     * @return void
     */
    public function setModuleSetting($moduleId, $varName, $varValue)
    {
        $this->_moduleData[$moduleId][$varName] = $varValue;
    }

    /**
     * Save module settings to database
     *
     * @param string $moduleId Module id
     *
     * @return bool
     */
    public function saveModuleSettings($moduleId)
    {
        if (empty($this->_moduleData[$moduleId])) {
            return true; // nothing to do
        }
        $settings = $this->_moduleData[$moduleId];
        $res      = true;
        $sql      = 'INSERT INTO `' . $this->_database . '`.`' . $this->_tableModuleConfig . '` '
            . '(`module_id`, `varname`, `varvalue`) VALUES (?,?,?) '
            . 'ON DUPLICATE KEY UPDATE `varvalue` = VALUES(`varvalue`)';
        $stmt     = $this->_dbo->prepare($sql);

        foreach ($settings as $varName => $varValue) {
            // we prefix arrays and objects with @@@ and save them serialized
            if (is_array($varValue) || is_object($varValue)) {
                $varValue = '@@@' . serialize($varValue);
            }

            if (empty($varValue)) {
                $varValue = '';
            }
            $stmt->bind_param('sss', $moduleId, $varName, $varValue);
            $stmt->execute();
            if ((int)$stmt->errno !== 0) {
                die($stmt->error);
                $res = false;
            }
        }
        $stmt->close();

        return $res;
    }

}

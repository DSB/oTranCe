<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Cli
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * Cli wrapper for calls via bash or cronjob
 *
 * @package         oTranCe
 * @subpackage      Cli
 */
class Cli
{
    /**
     * @var Msd_Vcs
     */
    protected $_vcs;

    /**
     * Constrctor
     *
     * @return Cli
     */
    public function __construct()
    {
        $this->_config = Msd_Registry::getConfig();
    }

    /**
     * Update vcs if project uses vcs
     *
     * @return void
     */
    public function updateVcs()
    {
        $this->_projectConfig = $this->_config->getParam('project');
        if ($this->_projectConfig['vcsActivated'] == 1) {
            echo "\nProject uses VCS. Updating ...\n";
            $vcs = $this->_getVcsInstance();
            $update = $vcs->update();
            if (!empty($update['stderr'])) {
                echo $update['stderr'];
                echo "\nError. Ending action.\n\n";
                exit(1);
            }
            echo $update['stdout'] . "\n";
        }
    }

    /**
     * Get vcs adapter instance
     *
     * @return Msd_Vcs_Interface VCS-Adapter instance
     */
    private function _getVcsInstance()
    {
        if ($this->_vcs === null) {
            $vcsConfig = $this->_config->getParam('vcs');
            $this->_vcs = Msd_Vcs::factory($vcsConfig['adapter'], $vcsConfig['options']);
        }

        return $this->_vcs;
    }

}

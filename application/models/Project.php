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
 * Project model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_Project extends Msd_Application_Model
{
    /**
     * Projects ids since tooling is done from .ini files
     */
    const DEFAULT_PROJECT = 'default';
    const DEFAULT_PROJECT_ID = 1;

    /**
     * Default project array from config
     *
     * @return array
     * @throws Msd_Exception
     */
    public function getDefaultProject()
    {
        $projects = $this->_config->getParam('project');

        if (empty($projects)) {
            throw new Msd_Exception('No project is configured.');
        }

        return $projects[self::DEFAULT_PROJECT];
    }

    /**
     * Get config values for all projects
     *
     * @return mixed
     */
    public function getAllProjects()
    {
        return $this->_config->getParam('project');
    }

    /**
     * Get config values for specified project
     *
     * @param string $project
     * @return array
     * @throws \Msd_Exception
     */
    public function getProject($project)
    {
        /** @var array $project */
        $projectConf = $this->_config->getParam('project');

        if (! array_key_exists($project, $projectConf)) {
            throw new Msd_Exception('Project: ' . $project . ' not found.');
        }

        return $projectConf[$project];
    }


    /**
     * Get project id by its key name definition in config
     *
     * @param string $project
     *
     * @return int
     * @throws \Msd_Exception
     */
    public function getProjectId($project)
    {
        /** @var array $projectConf */
        $projectConf = $this->getProject($project);
        return (int) $projectConf['id'];
    }


    /**
     * Return project setting by its Id
     *
     * @param int $projectId
     * @return array
     */
    public function getProjectById($projectId)
    {
        $result = array_filter(
            $this->getAllProjects(),
            function ($project) use ($projectId) {
                return (int) array_key_exists('id', $project)
                    && $project['id'] === $projectId;
            }
        );

        if (empty($result)) {
            return array();
        }

        return array_shift($result);
    }

}

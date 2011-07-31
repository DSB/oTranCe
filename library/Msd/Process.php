<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         oTranCe
 * @subpackage      Process
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Wrapper class for the PHP function "proc_open". It encapsulates setting of working directory, environment variables,
 * writing to the standard input, reading from the standard output and reading from the standard error stream.
 * All handles are closed automatically if the instance is destroyed or the "close" method is called.
 *
 * @package         oTranCe
 * @subpackage      Process
 */
class Msd_Process
{
    /**
     * Command to execute.
     *
     * @var string
     */
    private $_command = null;

    /**
     * Working directory for the child process.
     *
     * @var string
     */
    private $_workDir = null;

    /**
     * Environment variables for the child process.
     *
     * @var array
     */
    private $_envVariables = null;

    /**
     * Resource handle of the child process.
     *
     * @var Resource
     */
    private $_process = null;

    /**
     * File handles to the process I/O channels.
     * 0 => STDIN  write only
     * 1 => STDOUT read only
     * 2 => STDERR read only
     *
     * @var array
     */
    private $_pipes = array(
        0 => null,
        1 => null,
        2 => null,
    );

    /**
     * Class constructor.
     * Set the command, working directory and environment variables for the execution.
     *
     * @param string $command Complete command to execute.
     * @param string $workDir Working directory for the child process.
     * @param array  $envVars Environment variables for the child process.
     */
    public function __construct($command = null, $workDir = null, array $envVars = null)
    {
        if ($command !== null) {
            $this->_command = $command;
        }
        
        if ($workDir !== null && file_exists($workDir)) {
            $this->_workDir = realpath((string) $workDir);
        } else {
            $this->_workDir = getcwd();
        }

        if ($envVars !== null) {
            $this->_envVariables = $envVars;
        }
    }

    /**
     * Close all I/O pipes and the process.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Executes the given command and stores the pipes and process handles.
     *
     * @return void
     */
    public function execute()
    {
        $pipesDesc = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
        unset($this->_pipes);
        $this->_process = proc_open(
            $this->_command,
            $pipesDesc,
            $this->_pipes,
            $this->_workDir,
            $this->_envVariables
        );
    }

    /**
     * Closes all handles that are associated to the running process.
     *
     * @return void
     */
    public function close()
    {
        if (is_resource($this->_process)) {
            foreach (array_keys($this->_pipes) as $pipeId) {
                if (is_resource($this->_pipes[$pipeId])) {
                    fclose($this->_pipes[$pipeId]);
                }
            }
            proc_close($this->_process);
        }
    }

    /**
     * Writes a string to the standard input stream of the running process.
     *
     * @param string $input String to write.
     *
     * @return void
     */
    public function writeInput($input)
    {
        if (is_resource($this->_pipes[0])) {
            fwrite($this->_pipes[0], $input);
        }
    }

    /**
     * Reads a line from standard output stream of the process and returns it. If the process
     *
     * @return null|string
     */
    public function readOutput()
    {
        $output = null;
        if (is_resource($this->_pipes[1])) {
            $output = fgets($this->_pipes[1]);
        }

        return $output;
    }

    /**
     * Reads a line from standard error stream of the process and returns it. If the process
     *
     * @return null|string
     */
    public function readError()
    {
        $error = null;
        if (is_resource($this->_pipes[2])) {
            $error = fgets($this->_pipes[2]);
        }

        return $error;
    }

    /**
     * Determines the running status of the child process.
     *
     * @return bool
     */
    public function isRunning()
    {
        if (!is_resource($this->_process)) {
            return false;
        }

        $procStatus = proc_get_status($this->_process);
        return $procStatus['running'];
    }

    /**
     * Sets a new command for execution.
     * The process must not be run!
     *
     * @param string $command Complete command.
     *
     * @return void
     */
    public function setCommand($command)
    {
        if (!$this->isRunning()) {
            $this->_command = $command;
        }
    }

    /**
     * Sets the working directory for the new process.
     * The working directory must exists and the process must not be run!
     *
     * @param string $workDir New working directory.
     *
     * @return void
     */
    public function setWorkDir($workDir)
    {
        if (file_exists($workDir) && !$this->isRunning()) {
            $this->_workDir = realpath((string) $workDir);
        }
    }

    /**
     * Sets the environment variables for the new process.
     * The process must not be run!
     *
     * @param array $envVars Environment variables.
     *
     * @return void
     */
    public function setEnvVariables(array $envVars)
    {
        if (!$this->isRunning()) {
            $this->_envVariables = $envVars;
        }
    }

    /**
     * Gets the command, which was or will be executed.
     *
     * @return null|string
     */
    public function getCommand()
    {
        return $this->_command;
    }

    /**
     * Gets the working directory, where process is or will be executed.
     *
     * @return null|string
     */
    public function getWorkDir()
    {
        return $this->_workDir;
    }

    /**
     * Gets the environment variables of the child process.
     *
     * @return array|null
     */
    public function getEnvVariables()
    {
        return $this->_envVariables;
    }
}

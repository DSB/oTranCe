<?php
/**
 * Class for Subversion support.
 */
class Msd_Vcs_Subversion implements Msd_Vcs_Interface
{
    /**
     * Username for SVN operations.
     *
     * @var string
     */
    private $_username = null;

    /**
     * Password for SVN operations.
     *
     * @var string
     */
    private $_password = null;

    /**
     * Path to the ckecked out repository
     *
     * @var string
     */
    private $_checkoutPath = null;

    /**
     * Parameters to add to every svn exection.
     *
     * @var string
     */
    private $_execParams = ' ';

    /**
     * Instance of Msd_Process class.
     *
     * @var Msd_Process
     */
    private $_process = null;

    /**
     * Mapping array of the svn status codes.
     *
     * @var array
     */
    private $_statusKeys = array(
        'M' => 'modified',
        'A' => 'added',
        'D' => 'deleted',
        'U' => 'updated',
        'C' => 'conflicted',
        'G' => 'merged',
        '!' => 'error',
        '?' => 'unversioned',
    );

    /**
     * Class constructor
     *
     * @param array $adapterParams Array with specific options.
     */
    public function __construct($adapterParams)
    {
        if (isset($adapterParams['username'])) {
            $this->_username = $adapterParams['username'];
        }

        if (isset($adapterParams['password'])) {
            $this->_password = $adapterParams['password'];
        }

        if (isset($adapterParams['checkoutPath'])) {
            $this->_checkoutPath = $adapterParams['checkoutPath'];
        } else {
            $this->_checkoutPath = getcwd();
        }

        if (isset($adapterParams['execParams'])) {
            $this->_execParams .= $adapterParams['execParams'];
        }

        $this->_process = new Msd_Process();
    }

    /**
     * Add one or more files to the repository.
     *
     * @param array $filenames File- and pathnames to add.
     *
     * @return array
     */
    public function add($filenames)
    {
        $filenames = (array) $filenames;
        return $this->_executeSvnCommand('add', $filenames);
    }

    /**
     * Remove one or more files from the repository.
     *
     * @param array $filenames File- and pathnames to delete.
     *
     * @return array
     */
    public function delete($filenames)
    {
        $filenames = (array) $filenames;
        return $this->_executeSvnCommand('delete', $filenames);
    }

    /**
     * Retrieves the subversion status of the checked out repository.
     *
     * @return array
     */
    public function status()
    {
        $rawSvnStatus = $this->_executeSvnCommand('status');
        $lines = explode(PHP_EOL, $rawSvnStatus['stdout']);
        $svnStatus = array();
        foreach ($lines as $line) {
            if (preg_match('/^([MACUDG!\?]).+ (.+)$/', $line, $args)) {
                $svnStatus[$this->_getStatusKey($args[1])][] = $args[2];
            }
        }
        return $svnStatus;
    }

    /**
     * Commit file and path changes to the repository.
     *
     * @param array  $filenames File- and pathnames to commit.
     * @param string $comment   Comment for the comit.
     *
     * @return array
     */
    public function commit($filenames, $comment = null)
    {
        $filenames = (array) $filenames;
        $params = array();
        if ($comment !== null) {
            $params['m'] = $comment;
        }
        return $this->_executeSvnCommand('commit', $filenames, $params);
    }

    /**
     * Updates local repository.
     *
     * @return array
     */
    public function update()
    {
        return $this->_executeSvnCommand('update');
    }

    /**
     * Undo file changes.
     *
     * @param array $filenames File- and pathnames for undo action.
     *
     * @return array
     */
    public function revert($filenames)
    {
        $filenames = (array) $filenames;
        return $this->_executeSvnCommand('revert', $filenames);
    }

    /**
     * Builds and executes a svn command.
     *
     * @throws Msd_Vcs_Subversion_Exception
     *
     * @param string $command   Subversion command
     * @param array  $filenames File- and pathnames
     * @param array  $params    Command specific parameters
     *
     * @return array
     */
    private function _executeSvnCommand($command, $filenames = array(), $params = array())
    {
        $filenames = (array) $filenames;
        $svnCommand = "svn " . $command;

        $svnCommand .= $this->buildParams($params);

        foreach ($filenames as $file) {
            $svnCommand .= ' ' . escapeshellarg($file);
        }
        $this->_process->setCommand($svnCommand);
        $this->_process->setWorkDir($this->_checkoutPath);
        $this->_process->execute();
        $stdout = '';
        $stderr = '';
        while ($this->_process->isRunning()) {
            $stdout .= $this->_process->readOutput();
            $stderr .= $this->_process->readError();
        }

        return array(
            'stderr' => $stderr,
            'stdout' => $stdout,
        );
    }

    /**
     * Builds command line string for the given parameters.
     *
     * @param array $params Parameters for command line
     *
     * @return string
     */
    private function buildParams($params)
    {
        $cmdParams = $this->_execParams;
        if ($this->_username !== null && strlen($this->_username) > 0) {
            $cmdParams .= ' --username ' . escapeshellarg($this->_username);
        }
        if ($this->_password !== null && strlen($this->_password) > 0) {
            $cmdParams .= ' --password ' . escapeshellarg($this->_password);
        }

        foreach ($params as $paramName => $paramValue) {
            $cmdParams .= ' -';
            if (strlen($paramName) > 1) {
                $cmdParams .= '-';
            }
            $cmdParams .= $paramName . ' ' . escapeshellarg($paramValue);
        }
        return $cmdParams;
    }

    /**
     * Returns the status key for a char, returned by "svn st".
     *
     * @param string $charStatus
     *
     * @return string
     */
    private function _getStatusKey($charStatus)
    {
        $statusKey = 'unknown';
        if (isset($this->_statusKeys[$charStatus])) {
            $statusKey = $this->_statusKeys[$charStatus];
        }

        return $statusKey;
    }

    /**
     * Returns available options for the adapter.
     *
     * @static
     *
     * @abstract
     *
     * @return array
     */
    public static function getAdapterOptions()
    {
        return array(
            'checkoutPath' => 'SVN Checkout path',
            'username' => 'SVN Username',
            'password' => 'SVN Password',
            'execParams' => 'SVN execution parameters',
        );
    }

    /**
     * Returns the adapter option names for user specific credentials.
     *
     * @static
     *
     * @abstract
     *
     * @return array
     */
    public static function getCredentialFields()
    {
        return array(
            'username' => 'username',
            'password' => 'password',
        );
    }
}

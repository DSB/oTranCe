<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Vcs
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * VCS subversion support class
 *
 * @package         MySQLDumper
 * @subpackage      Vcs
 */
class Msd_Vcs_Git implements Msd_Vcs_Interface
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
     * Name of the branch inside the GIT repository.
     *
     * @var string
     */
    private $_branchName = null;

    /**
     * URL to the GIT repository;
     *
     * @var string
     */
    private $_remoteUrl = null;

    /**
     * Instance of Msd_Process class.
     *
     * @var Msd_Process
     */
    private $_process = null;

    /**
     * Information about the author.
     *
     * @var stdClass
     */
    private $_author = null;

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
    public function __construct($adapterParams = array())
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

        if (isset($adapterParams['branchName'])) {
            $this->_branchName = $adapterParams['branchName'];
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
        if (count($filenames) == 0) {
            $filenames[] = '.';
        }

        return $this->_executeCommand('add', $filenames);
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

        return $this->_executeCommand('delete', $filenames);
    }

    /**
     * Retrieves the subversion status of the checked out repository.
     *
     * @return array
     */
    public function status()
    {
        $rawSvnStatus = $this->_executeCommand('status', array(), array('porcelain' => false));
        if (isset($rawSvnStatus['stderr']) && $rawSvnStatus['stderr'] > '') {
            // we got an error - return it to let the view show it
            return $rawSvnStatus;
        }

        // extract information about files to add, ect.
        $lines  = explode(PHP_EOL, $rawSvnStatus['stdout']);
        $status = array();
        foreach ($lines as $line) {
            if (preg_match("/^([MACUDG!?])\\S*\\s+(.+)$/", ltrim($line), $args)) {
                $status[$this->_getStatusKey($args[1])][] = $args[2];
            }
        }

        return $status;
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
        $this->add($filenames);

        $params = array();

        if ($this->_author !== null) {
            $this->_executeCommand('config', array('user.name', $this->_author->name));
            $this->_executeCommand('config', array('user.email', $this->_author->email));
        }

        if ($comment === null) {
            $comment = 'oTranCe: Language pack updated.';
        }
        $params['v'] = false;
        $params['m']  = $comment;
        $commitResult = $this->_executeCommand('commit', array(), $params);
        $originUrl = $this->_getRemoteUrl();

        $commandArguments = array($originUrl);

        if ($this->_branchName !== null && strlen($this->_branchName) > 0) {
            $commandArguments[] = $this->_branchName;
        }

        $pushResult = $this->_executeCommand('push', $commandArguments);
        if (substr($pushResult['stderr'], 0, 3) == 'To ') {
            $pushResult['stdout'] = 'Success.';
            $pushResult['stderr'] = '';
        }

        return array_merge($commitResult, $pushResult);
    }

    /**
     * Updates local repository.
     *
     * @return array
     */
    public function update()
    {
        return $this->_executeCommand('pull');
    }

    /**
     * Drop file changes.
     *
     * @param array $filenames File- and pathnames for undo action.
     *
     * @return array
     */
    public function revert($filenames)
    {
        $filenames = (array) $filenames;
        $this->_executeCommand(
            'reset',
            array_merge(array('HEAD'), $filenames)
        );

        return $this->_executeCommand(
            'checkout',
            array_merge(array('--'), $filenames)
        );
    }

    /**
     * Sets the information about the author.
     *
     * @param string $authorName  Name of the author.
     * @param string $authorEMail E-mail address of the author.
     *
     * @return void
     */
    public function setAuthor($authorName, $authorEMail)
    {
        $this->_author = new stdClass();
        $this->_author->name = $authorName;
        $this->_author->email = $authorEMail;
    }

    /**
     * Builds and executes a svn command.
     *
     * @throws Msd_Vcs_Subversion_Exception
     *
     * @param string $command   Subversion command
     * @param array  $fileNames File- and pathnames
     * @param array  $params    Command specific parameters
     *
     * @return array
     */
    private function _executeCommand($command, $fileNames = array(), $params = array())
    {
        $fileNames  = (array) $fileNames;
        $gitCommand = 'git ' . $command;

        $gitCommand .= $this->buildParams($params);

        foreach ($fileNames as $file) {
            $gitCommand .= ' ' . escapeshellarg($file);
        }

        $this->_process->setCommand($gitCommand);
        $this->_process->setWorkDir($this->_checkoutPath);
        $this->_process->execute();
        $stdout = '';
        $stderr = '';
        while ($this->_process->isRunning()) {
            $stdout .= $this->_process->readOutput();
            $stderr .= $this->_process->readError();
        }

        $this->_process->close();

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
//        if ($this->_username !== null && strlen($this->_username) > 0) {
//            $cmdParams .= ' --username ' . escapeshellarg($this->_username);
//        }
//        if ($this->_password !== null && strlen($this->_password) > 0) {
//            $cmdParams .= ' --password ' . escapeshellarg($this->_password);
//        }

        foreach ($params as $paramName => $paramValue) {
            $cmdParams .= ' -';
            $valueSeparator = ' ';
            if (strlen($paramName) > 1) {
                $cmdParams .= '-';
                $valueSeparator = '=';
            }
            $cmdParams .= $paramName;
            if ($paramValue !== false) {
                $cmdParams .= $valueSeparator . escapeshellarg($paramValue);
            }
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
     * @abstract
     *
     * @return array
     */
    public function getAdapterOptions()
    {
        return array(
            'checkoutPath' => 'Git Checkout path',
            'username'     => 'Git username',
            'password'     => 'Git password',
            'execParams'   => 'Git execution parameters',
            'branchName'   => 'Name of the translation branch'
        );
    }

    /**
     * Returns the adapter option names for user specific credentials.
     *
     * @abstract
     *
     * @return array
     */
    public function getCredentialFields()
    {
        return array(
            'username' => 'username',
            'password' => 'password',
        );
    }

    /**
     * Returns the remote URL to the GIT repository.
     *
     * @return string
     */
    private function _getRemoteUrl()
    {
        if ($this->_remoteUrl !== null) {
            return $this->_remoteUrl;
        }

        $configResult     = $this->_executeCommand('config', array('remote.origin.url'));
        $this->_remoteUrl = 'origin';
        $originUrlParts   = parse_url(trim($configResult['stdout']));

        if (isset($originUrlParts['scheme']) && false !== strpos($originUrlParts['scheme'], 'http')) {
            if ($this->_username !== null && strlen($this->_username) > 0) {
                $originUrlParts['user'] = $this->_username;
            }

            if ($this->_password !== null && strlen($this->_username) > 0) {
                $originUrlParts['pass'] = $this->_password;
            }

            $this->_remoteUrl = $originUrlParts['scheme'] . "://";
            if (isset($originUrlParts['user'])) {
                $this->_remoteUrl .= urlencode($originUrlParts['user']);
            }

            if (isset($originUrlParts['pass'])) {
                $this->_remoteUrl .= ':' . urlencode($originUrlParts['pass']);
            }

            if (isset($originUrlParts['user'])) {
                $this->_remoteUrl .= "@";
            }

            $this->_remoteUrl .= $originUrlParts['host'];

            if (isset($originUrlParts['port'])) {
                $this->_remoteUrl .= ':' . $originUrlParts['port'];
            }

            $this->_remoteUrl .= '/' . ltrim($originUrlParts['path'], '/');

            if (isset($originUrlParts['query'])) {
                $this->_remoteUrl .= '?' . $originUrlParts['query'];
            }

            if (isset($originUrlParts['fragment'])) {
                $this->_remoteUrl .= '#' . $originUrlParts['fragment'];
            }
        }

        return $this->_remoteUrl;
    }
}

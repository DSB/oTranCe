<?php
class Msd_Archive
{
    /**
     * Disabled class constructor.
     */
    private function __construct()
    {
    }

    /**
     * Create a new instance of an archive adapter and returns it.
     *
     * @static
     * @param string $archiveAdapter  Name of the archive adapter
     * @param string $baseArchiveName Path- and filename of the resulting archive, without extension.
     * @param string $workingDir      Working directory for the archive adapter.
     *
     * @return Msd_Archive_Abstract
     */
    public static function factory($archiveAdapter, $baseArchiveName, $workingDir = null)
    {
        if ($workingDir === null) {
            $workingDir = getcwd();
        }
        $adapterClass = 'Msd_Archive_' . $archiveAdapter;
        $adapterInstance = new $adapterClass($baseArchiveName, $workingDir);
        if (!$adapterInstance instanceof Msd_Archive_Abstract) {
            throw new Msd_Archive_Exception('The archive adapter must extend Msd_Archive_Abstract.');
        }
        return $adapterInstance;
    }
}

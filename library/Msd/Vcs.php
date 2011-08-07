<?php
class Msd_Vcs
{
    private function __construct()
    {
    }

    /**
     * @static
     * @throws Msd_Vcs_Exception
     * @param $vcsName
     * @param array $adapterOptions
     * @return Msd_Vcs_Interface
     */
    public static function factory($vcsName, $adapterOptions = array())
    {
        $vcsNameParts = explode('_', $vcsName);
        foreach (array_keys($vcsNameParts) as $key) {
            $vcsNameParts[$key] = ucfirst($vcsNameParts[$key]);
        }
        $vcsClass = 'Msd_Vcs_' . implode('_', $vcsNameParts);
        $vcs = new $vcsClass($adapterOptions);
        if (!$vcs instanceof Msd_Vcs_Interface) {
            throw new Msd_Vcs_Exception("The specified VCS adapter doesn't implement the interface Msd_Vcs_Interface.");
        }

        return $vcs;
    }
}

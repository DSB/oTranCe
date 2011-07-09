<?php

class Application_Model_Analyzer
{
    /**
     * Get list of available import analyzers
     *
     * @return array
     */
    public function getAvailableImportAnalyzers()
    {
        $analyzers = array();

        $dir = new DirectoryIterator(APPLICATION_PATH . '/models/Importer');
        foreach ($dir as $fileinfo) {
        $pos = strrpos($fileinfo->getFilename(), '.php');
            if ($pos !== false) {
                $name = substr($fileinfo->getFilename(), 0, $pos);
                $analyzers["$name"] =  $name;
            }
        }

        return $analyzers;
    }
}

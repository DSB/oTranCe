<?php
/**
 * Interface definition for language file importer.
 */
interface Msd_Import_Interface
{
    /**
     * Analyze data and return exracted key=>value pairs
     *
     * @abstract
     * @param string $data String data to analyze
     *
     * @return array Extracted key => value-Array
     */
    public function extract($data);

    /**
     * Get rendered info view
     *
     * @return string
     */
    public function getInfo();

}

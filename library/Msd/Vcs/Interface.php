<?php
/**
 * Interface definition for VCS support classes.
 */
interface Msd_Vcs_Interface
{
    /**
     * Creates and initializes a new instance of the adapter class.
     *
     * @abstract
     *
     * @param array $adapterParams Array with adapter specific parameters
     *
     * @return \Msd_Vcs_Interface
     */
    public function __construct($adapterParams = array());

    /**
     * Adds a file to the VCS.
     *
     * @abstract
     *
     * @param string $filename
     *
     * @return void
     */
    public function add($filename);

    /**
     * Deletes a file from the VCS.
     *
     * @abstract
     *
     * @param string $filename
     *
     * @return void
     */
    public function delete($filename);

    /**
     * Retrieves the current status of the files in VCS.
     *
     * @abstract
     *
     * @return array
     */
    public function status();

    /**
     * Commit changes to VCS server.
     *
     * @abstract
     *
     * @param array  $filenames
     * @param string $comment
     *
     * @return void
     */
    public function commit($filenames, $comment = null);

    /**
     * Update the loacl repository.
     *
     * @abstract
     *
     * @return void
     */
    public function update();

    /**
     * Undo file changes.
     *
     * @abstract
     *
     * @param array $filenames
     *
     * @return void
     */
    public function revert($filenames);

    /**
     * Returns available options for the adapter.
     *
     * @abstract
     *
     * @return array
     */
    public function getAdapterOptions();

    /**
     * Returns the adapter option names for user specific credentials.
     *
     * @abstract
     *
     * @return array
     */
    public function getCredentialFields();
}

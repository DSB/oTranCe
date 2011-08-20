<?php
/**
 * Interface definition for VCS support classes.
 */
interface Msd_Vcs_Interface
{
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
     * @return void
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
     * Ubdo file changes.
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
     * @static
     *
     * @abstract
     *
     * @return array
     */
    public static function getAdapterOptions();

    /**
     * Returns the adapter option names for user specific credentials.
     *
     * @static
     *
     * @abstract
     *
     * @return array
     */
    public static function getCredentialFields();
}

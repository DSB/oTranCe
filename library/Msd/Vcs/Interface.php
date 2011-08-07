<?php
interface Msd_Vcs_Interface
{
    public function add($filename);
    public function delete($filename);
    public function status();
    public function commit($filenames, $comment = null);
    public function update();
    public function revert($filenames);
}

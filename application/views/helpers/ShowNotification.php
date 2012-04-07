<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 * @version         SVN: $Rev$
 * @author          $Author$
 */

/**
 * Show a notification message that automaticallay blends out
 *
 * @package         MySQLDumper
 * @subpackage      View_Helpers
 */
class Msd_View_Helper_ShowNotification extends Zend_View_Helper_Abstract
{
    /**
     * Show notification.
     * Prepends an icon according to the state of $success and adds javascript to show the message.
     *
     * @param bool   $success      Whether the save action was successful
     * @param string $okMessage    Message to show in case success is true
     * @param string $errorMessage Message to show in case success is false
     * @param int    $duration     Time to show the mnessage in milliseconds
     *
     * @return void
     */
    public function showNotification($success, $okMessage, $errorMessage, $duration = 2000)
    {
        if ($success === true) {
            $class = 'ok';
            $content = $this->view->getIcon('Ok', '', 16) . ' ' . $okMessage;
        } else {
            $class = 'error';
            $content = $this->view->getIcon('Attention', '', 16) .' ' . $errorMessage;
        }
        $this->view->jQuery()->onLoadCaptureStart(); ?>
        var notify = '<div id="notify" class="notification-bar <?php echo $class;?>"><?php echo $this->view->jsEscape($content);?></div>';
        $('body').append(notify);
        $('#notify').delay(200).fadeIn(600).delay(<?php echo $duration;?>).fadeOut(600);
        <?php
        $this->view->jQuery()->onLoadCaptureEnd();
    }
}
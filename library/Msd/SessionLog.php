<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://www.otrance.org
 *
 * @package         oTranCe
 * @subpackage      SessionLog
 */

/**
 * Class for handling messages that are stored in the session
 *
 * @package         oTranCe
 * @subpackage      SessionLog
 */
class Msd_SessionLog
{
    /**
     * @var Zend_Session_Namespace
     */
    protected $log;

    /**
     * Constructor
     *
     * @param string $name Identifier
     */
    public function __construct($name)
    {
        $this->log = new Zend_Session_Namespace($name, true);
    }

    /**
     * Remove all messages
     */
    public function clear()
    {
        $this->log = array();
    }

    /**
     * Add a message to type array
     *
     * @param string $message The message
     * @param string $type    The log type
     */
    public function addMessage($message, $type)
    {
        if (!isset($this->log->$type)) {
            $this->log->$type = array();
        }

        $messages         = $this->log->$type;
        $messages[]       = $message;
        $this->log->$type = $messages;
        var_dump($this->log);
    }

    /**
     * Get all messages of given type
     *
     * @param string $type
     *
     * @return array
     */
    public function getMessagesOfType($type)
    {
        if (!isset($this->log->$type)) {
            return array();
        }

        return $this->log->$type;
    }

}
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
    const
        TYPE_SUCCESS = 'success',
        TYPE_WARNING = 'warning',
        TYPE_ERROR   = 'error';

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
        $this->log->unsetAll();
    }

    /**
     * Add a message to type array
     *
     * @param string $type    The log type
     * @param string $message The message
     */
    public function addMessage($type, $message)
    {
        if (!isset($this->log->$type)) {
            $this->log->$type = array();
        }

        array_push($this->log->$type, $message);
    }

    /**
     * Saves array of messages to session log.
     * Expected is: array(
     *   self::type => 'Message',
     *   ...
     * );
     *
     * @param array $logMessages
     */
    public function addMessages($logMessages)
    {
        foreach ($logMessages as $type => $messages) {
            foreach ($messages as $message) {
                $this->addMessage($message, $type);
            }
        }
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

    /**
     * Get number of log messages
     *
     * @return int
     */
    public function getMessageCount()
    {
        $messagesCount = count($this->getMessagesOfType(self::TYPE_SUCCESS));
        $messagesCount += count($this->getMessagesOfType(self::TYPE_WARNING));
        $messagesCount += count($this->getMessagesOfType(self::TYPE_ERROR));

        return $messagesCount;
    }
}
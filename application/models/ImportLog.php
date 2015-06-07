<?php
/**
 * This file is part of oTranCe released under the GNU GPL 2 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Models
 * @version         SVN: $
 * @author          $Author$
 */

/**
 * Importer log model
 *
 * Handles log messages.
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_ImportLog
{
    const
        TYPE_SUCCESS = 'success',
        TYPE_WARNING = 'warning',
        TYPE_ERROR   = 'error';

    /**
     * @var Msd_SessionLog
     */
    protected $log;

    /**
     * @var int
     */
    protected $languageId;

    /**
     * @var int
     */
    protected $fileTemplate;

    /**
     * Constructor
     */
    public function __construct($languageId, $fileTemplate)
    {
        $this->log          = new Msd_SessionLog('importLog');
        $this->languageId   = $languageId;
        $this->fileTemplate = $fileTemplate;
    }

    /**
     * Add a log message
     *
     * @param string $type
     * @param string $key
     * @param string $message
     *
     * @return void
     */
    public function addMessage($type, $key, $message)
    {
        $this->log->addMessage($type, $this->_getMessageArray($key,$message));
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
        return $this->log->getMessagesOfType($type);
    }

    /**
     * Compose message log array
     *
     * @param string $key
     * @param string $message
     *
     * @return array
     */
    protected function _getMessageArray($key, $message)
    {
        return array(
            'templateId' => $this->fileTemplate,
            'languageId' => $this->languageId,
            'key'        => $key,
            'message'    => $message,
        );
    }
}
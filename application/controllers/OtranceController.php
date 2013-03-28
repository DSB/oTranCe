<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          Daniel Schlichtholz <admin@mysqldumper.de>
 */
/**
 * Main Controller that is extended by other controllers.
 * Used to provide some general methods.
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class OtranceController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_User
     */
    protected $_userModel;

    /**
     * @var Msd_Config
     */
    protected $_config;

    /**
     * @var \Msd_Config_Dynamic
     */
    protected $_dynamicConfig;

    /**
     * Class constructor
     *
     * @see Zend_Controller_Action
     *
     * @param Zend_Controller_Request_Abstract  $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array                             $invokeArgs Any additional invocation arguments
     *
     * @return OtranceController
     */
    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array()
    )
    {
        $this->_config        = Msd_Registry::getConfig();
        $this->_dynamicConfig = Msd_Registry::getDynamicConfig();
        $this->_userModel = new Application_Model_User();
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Checks if the current user has permissions for given right.
     * If not, he is forwarded to the "not allowed" action of the error controller.
     *
     * @param string $right Name of right to check
     *
     * @return bool
     */
    public function checkRight($right)
    {
        if (!$this->_userModel->hasRight($right)) {
            $this->forward('not-allowed', 'Error', null);

            return false;
        }

        return true;
    }
}
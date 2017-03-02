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
     * Hard coding projects ids since tooling is done from .ini files
     */
    const DEFAULT_PROJECT_ID = 'default';

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
     * @param Zend_Controller_Request_Abstract  $request    Request instance
     * @param Zend_Controller_Response_Abstract $response   Response instance
     * @param array                             $invokeArgs Any additional invocation arguments
     *
     * @see Zend_Controller_Action
     */
    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array()
    )
    {
        $this->_config        = Msd_Registry::getConfig();
        $this->_dynamicConfig = Msd_Registry::getDynamicConfig();

        if ($this->_dynamicConfig->getParam('activeProject') === null) {
            $this->_dynamicConfig->setParam('activeProject', self::DEFAULT_PROJECT_ID);
        }

        $this->_userModel     = new Application_Model_User();
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

    /**
     * Check sort field for allowed values to prevent SQL-injection.
     * Fall back to 'username' for invalid values.
     *
     * @param string $sortField Selected sort field
     *
     * @return string Validated sort field
     */
    protected function getValidatedSortField($sortField)
    {
        $allowedValues = array('id', 'username', 'realName', 'active', 'editActions', 'lastAction', 'locale');
        if (in_array($sortField, $allowedValues)) {
            return $sortField;
        }

        return 'username';
    }

}

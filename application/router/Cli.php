<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package    oTranCe
 * @subpackage Routers
 * @version    SVN: $Rev$
 * @author     Daniel Schlichtholz <admin@mysqldumper.de>
 */
/**
 * Router for calls via cli
 *
 * @package    oTranCe
 * @subpackage Routers
 */
class Application_Router_Cli extends Zend_Controller_Router_Abstract
{
    /**
     * Processes a request and sets its controller and action.  If
     * no route was possible, an exception is thrown.
     *
     * @see Zend_Controller_Router_Interface
     *
     * @param Zend_Controller_Request_Abstract $dispatcher Request instance
     *
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Request_Abstract|boolean
     */
    public function route(Zend_Controller_Request_Abstract $dispatcher)
    {
        $opts = new Zend_Console_Getopt(
            array(
                 'username|u=s'   => 'The user name to use in oTranCe.',
                 'pass|p=s'       => 'The password of the user in oTranCe.',
                 'controller|c=s' => 'The controller to call.',
                 'action|a-s'     => 'The action of the controller to call. If not given defauls to "index".',
                 'help|h'         =>
                 'Show this help. Example call: php index.php -u User -p Password -c Export -a update-all'
            )
        );

        try {
            $opts->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getUsageMessage();
            exit(255);
        }

        $controller = $opts->getOption('c');
        $action     = $opts->getOption('a');
        $userName   = $opts->getOption('u');
        $userPass   = $opts->getOption('p');
        if (empty($controller) || empty($userName) || empty($userPass)) {
            echo $opts->getUsageMessage();
            exit(255);
        }
        if (empty($action)) {
            $action = 'index';
        }

        $this->_loginUser($userName, $userPass);

        echo "\nExecuting: " . $action . " action of controller " . $controller . "\n";

        $dispatcher->setControllerName($controller);
        $dispatcher->setActionName($action);
        // pass through remaining args
        $dispatcher->setParams($opts->getRemainingArgs());

        return $dispatcher;
    }

    /**
     * Generates a URL path that can be used in URL creation, redirection, etc.
     *
     * @param array $userParams Options passed by a user used to override parameters
     * @param mixed $name       The name of a Route to use
     * @param bool  $reset      Whether to reset to the route defaults ignoring URL params
     * @param bool  $encode     Tells to encode URL parts on output
     *
     * @throws Zend_Controller_Router_Exception
     * @see Zend_Controller_Router_Interface
     *
     * @return string Resulting URL path
     */
    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {
        echo "Not implemented\n", exit;
    }

    /**
     * Log in user under which the instance should run
     *
     * @param string $userName User name
     * @param string $userPass User password
     *
     * @return void
     */
    public function _loginUser($userName, $userPass)
    {
        $user        = new Msd_User();
        $loginResult = $user->login($userName, $userPass, 0);
        if ($loginResult != Msd_User::SUCCESS) {
            exit("\nCouldn't log in user " . $userName . ". Check username and/or password.");
        }
    }
}

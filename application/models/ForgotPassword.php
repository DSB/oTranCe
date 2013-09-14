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
 * User model
 *
 * @package         oTranCe
 * @subpackage      Models
 */
class Application_Model_ForgotPassword extends Msd_Application_Model
{
    /**
     * Name of table containing forgotpassword data
     *
     * @var
     */
    private $_tableForgotPassword;

    /**
     * Holds the generatedLinkHash value
     *
     * @var string
     */
    private $_generatedLinkHash;

    /**
     * Holds the lifeTime of a link - set in config
     *
     * @var int
     */
    private $_linkLifeTime;


    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $tableConfig               = $this->_config->getParam('table');
        $this->_tableForgotPassword = $tableConfig['forgotpasswords'];

        $projectConfig      = $this->_config->getParam('project');
        $this->_linkLifeTime = $projectConfig['forgotPasswordLinkLifeTime'];
    }

    /**
     * Generates hashed id for mail link
     *
     * @param int $user Id of user
     *
     * @return void
     */
    public function setLinkHashId($user)
    {
        $tempString = 'userid=' . $user['id'] . '&usermail=' . $user['email'] . '&id=' . $this->getLastInsertedId();

        $this->_generatedLinkHash = base64_encode($tempString);

    }

    /**
     * saves forgot password requests into db
     *
     * @param string $userId
     *
     * @return bool
     */
    public function saveRequest($userId)
    {
        $timeStamp = date('Y-m-d H:i:s', time());
        $sql       = 'INSERT INTO `' . $this->_tableForgotPassword . '` (`userid`, `timestamp`)'
                     . ' VALUES(' . $userId . ', \'' . $timeStamp . '\')';

        return $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * Get last inserted forgot password id
     *
     * @return bool|int
     */
    public function getLastInsertedId()
    {
        return $this->_dbo->getLastInsertId();
    }

    /**
     * Gets generated hash
     *
     * @return string
     */
    public function getGeneratedHashId()
    {
        return $this->_generatedLinkHash;
    }

    /**
     * Checks if request link is valid
     * 1st check: linkLifeTime expired
     * 2nd check: does the userid match the requested one?
     *
     * @param int $forgotPasswordId id of forgot password request
     * @param int $requestedUserId  id of forgot password user
     *
     * @return bool
     */
    public function isValidRequest($forgotPasswordId, $requestedUserId)
    {
        $sql = 'SELECT `timestamp`, `userid` FROM `' . $this->_tableForgotPassword . '` where id = ' . $forgotPasswordId;

        $requestTime = $this->_dbo->query($sql, Msd_Db::ARRAY_ASSOC);

        if (!$requestTime) {
            return false;
        }

        $requestTimeInSeconds = strtotime($requestTime[0]['timestamp']);
        $userId               = $requestTime[0]['userid'];
        $now                  = time();

        if ((($now - $requestTimeInSeconds) < $this->_linkLifeTime) && ($userId == $requestedUserId)) {
            return true;
        }

        return false;
    }


    /**
     * Delete forgot password request from database
     *
     * @param int $userId Id of user
     *
     * @return void
     */
    public function deleteRequestByUserId($userId)
    {
        $sql = 'DELETE FROM `' . $this->_tableForgotPassword . '` where userid = ' . $userId;
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }
}

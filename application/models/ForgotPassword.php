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
     * Timestamp when password reset was called
     *
     * @var string
     */
    private $_timestamp;

    /**
     * Model initialization method.
     *
     * @return void
     */
    public function init()
    {
        $this->_tableForgotPassword = $this->_tablePrefix . 'forgotpasswords';
        $this->_linkLifeTime        = $this->_config->getParam('project.forgotPasswordLinkLifeTime');
        $this->setTimeStamp(date('Y-m-d H:i:s', time()));
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
        $params = array(
            'userid'    => $user['id'],
            'usermail'  => $user['email'],
            'id'        => $this->getLastInsertedId(),
            'timestamp' => $this->getTimestamp()
        );

        $this->_generatedLinkHash = base64_encode(http_build_query($params));
    }

    /**
     * saves forgot password requests into db
     *
     * @param string $userId Id of user
     *
     * @return bool
     */
    public function saveRequest($userId)
    {

        $sql       = 'INSERT INTO `' . $this->_tableForgotPassword . '` (`userid`, `timestamp`)'
            . ' VALUES(' . $userId . ', \'' . $this->getTimestamp() . '\')';

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
     * 3rd check: does the timestamp match?
     *
     * @param int $forgotPasswordId id of forgot password request
     * @param int $requestedUserId  id of forgot password user
     *
     * @return bool
     */
    public function isValidRequest($forgotPasswordId, $requestedUserId, $timestamp)
    {
        $sql = 'SELECT `timestamp`, `userid` FROM `' . $this->_tableForgotPassword
            . '` where id = ' . (int) $forgotPasswordId . ' AND `timestamp` = \'' . $timestamp .'\'';

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
        $sql = 'DELETE FROM `' . $this->_tableForgotPassword . '` where userid = ' . (int) $userId;
        $this->_dbo->query($sql, Msd_Db::SIMPLE);
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->_timestamp = $timestamp;
    }
}

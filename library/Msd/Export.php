<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 21.04.11
 * Time: 10:48
 * To change this template use File | Settings | File Templates.
 */

class Msd_Export
{
    /**
     * Name of subversion user
     * @var string
     */
    private $_svnUser;

    /**
     * Password of subversion user
     * @var string
     */
    private $_svnPassword;

    /**
     * Commit message when changing one language
     * @var string
     */
    private $_commitMessageOneLanguage;

    /**
     * Commit message when changing all languages
     * @var string
     */
    private $_commitMessageAllLanguages;

    public function __construct()
    {
        $config = Msd_Configuration::getInstance();
        $this->_svnUser = $config->get('config.subversion.user');
        $this->_svnPassword = $config->get('config.subversion.password');
        $this->_commitMessageOneLanguage = $config->get('config.subversion.commitMessageOneLanguage');
        $this->_commitMessageAllLanguages = $config->get('config.subversion.commitMessageAllLanguages');

    }

    /**
     * Get last change timestamp of a file
     *
     * @param string $language
     * @return int
     */
    public function getFileTimestamp($language) {
        clearstatcache();
        $mTime = @filemtime(EXPORT_PATH . '/language/' . $language . '/lang.php');
        return $mTime;
    }

    /**
     * Convert a unix timestamp to date format
     *
     * @param  int $timestamp
     * @return string
     */
    public function convertUnixDate($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Export language vars from db to file
     *
     * @param string $language
     * @return int|false
     */
    public function exportLanguageFile($language)
    {
        $languageModel = new Application_Model_LanguageEntries();
        $data = $languageModel->getLanguageKeys($language);
        $english = $languageModel->getLanguageKeys('en'); // used as fallback for unmaintained vars
        $fileData = $this->_getFileHeader();
        foreach ($data as $key => $val) {
            if ($val == '') {
                $val = $english[$key];
            }
            $fileData .= '$lang[\'' . $key . '\']="' . $val . '";' . "\n";
        }
        $fileData .= $this->_getFileFooter();
        $res = false;
        $fh = fopen(EXPORT_PATH . DS . 'language' . DS . $language . DS . 'lang.php', 'wb');
        if ($fh) {
            $res = fwrite($fh, $fileData);
            fclose($fh);
        }
        return $res;
    }

    private function _getFileHeader()
    {
        $header = '<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package       MySQLDumper
 * @subpackage    Language
 * @version       $Rev$
 * @author        $Author$
 */
$lang=array();
';
        return $header;
    }

    private function _getFileFooter()
    {
        $footer = 'return $lang;' . "\n";
        return $footer;
    }

    /**
     * Commit language file to svn repository
     *
     * @param string $language
     * @return string
     */
    public function updateSvn($language)
    {
        $cmd = 'svn ci --username ' . $this->_svnUser . ' --password ' . $this->_svnPassword
                .' -m"' . sprintf($this->_commitMessageOneLanguage, $language) . '" '
               .' '.EXPORT_PATH.'/language/'.$language.'/lang.php';
        $res = shell_exec($cmd);
        if (trim($res == '')) {
            $res = 'Nothing to update.';
        }
        return $res;
    }

    /**
     * Commit all language files to svn repository
     *
     * @return string
     */
    public function updateSvnAll()
    {
        $cmd = 'svn ci --username ' . $this->_svnUser . ' --password ' . $this->_svnPassword
                .' -m"' . $this->_commitMessageAllLanguages . '" '
               . EXPORT_PATH.'/language';
        $res = shell_exec($cmd);
        if (trim($res == '')) {
            $res = 'Nothing to update.';
        }
        return $res;
    }
}

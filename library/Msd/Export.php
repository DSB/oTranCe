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

    /**
     * Array with file templates.
     *
     * @var array
     */
    private $_fileTemplates = array();

    public function __construct()
    {
        $config = Msd_Configuration::getInstance();
        $this->_svnUser = $config->get('config.subversion.user');
        $this->_svnPassword = $config->get('config.subversion.password');
        $this->_commitMessageOneLanguage = $config->get('config.subversion.commitMessageOneLanguage');
        $this->_commitMessageAllLanguages = $config->get('config.subversion.commitMessageAllLanguages');
        $fileTemplateModel = new Application_Model_FileTemplates();
        $this->_fileTemplates = $fileTemplateModel->getFileTemplatesAssoc();
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
    public function exportLanguageFile($language, $templateId = 1)
    {
        $languageModel = new Application_Model_Languages();
        $langInfo = $languageModel->getLanguageById($language);
        $langFilename = EXPORT_PATH . DS . trim(
            str_replace('{LOCALE}', $langInfo['locale'], $this->_fileTemplates[$templateId]['filename']),
            '/'
        );
        $langDir = dirname($langFilename);
        $fileLangVar = $this->_fileTemplates[$templateId]['content'];

        $languageEntriesModel = new Application_Model_LanguageEntries();
        $data = $languageEntriesModel->getLanguageKeys($language);
        $english = $languageEntriesModel->getLanguageKeys(2); // used as fallback for unmaintained vars

        if (!file_exists($langDir)) {
            mkdir($langDir, 0775, true);
        }
        $fh = fopen($langFilename, 'wb+');
        if (!$fh) {
            return false;
        }

        $res = (int) fwrite($fh, $this->_fileTemplates[$templateId]['header']);
        foreach ($data as $key => $val) {
            if ($val == '') {
                $val = $english[$key];
            }
            $res += (int) fwrite($fh, str_replace(array('{KEY}', '{VALUE}'), array($key, $val), $fileLangVar));
        }
        $res += (int) fwrite($fh, $this->_fileTemplates[$templateId]['footer']);
        fclose($fh);
        return ($res > 0) ? $res : false;
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

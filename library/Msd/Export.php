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

    /**
     * Array with filenames that are changed during the export process.
     *
     * @var array
     */
    private $_changedFiles = array();

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
     * 
     * @return int|false
     */
    public function exportLanguageFile($language)
    {
        $languageModel = new Application_Model_Languages();
        // retrieving fallback language,
        $fallbackLang = $languageModel->getFallbackLanguage();
        // if the fallback language isn't set, detect id for "English" and use it instead
        if ($fallbackLang === false) {
            $fallbackLang = $languageModel->getLanguageIdFromLocale('en');
        }
        $langInfo = $languageModel->getLanguageById($language);

        $languageEntriesModel = new Application_Model_LanguageEntries();
        $data = $languageEntriesModel->getLanguageKeys($language);
        $english = $languageEntriesModel->getLanguageKeys($fallbackLang); // used as fallback for unmaintained vars

        $langFileData = array();
        $res = 0;

        foreach ($data as $key => $entry) {
            $templateId = $entry['templateId'];
            // Did we have the meta data for the exported language file? If not, we will create it now.
            if (!isset($langFileData[$templateId])) {
                $langFilename = EXPORT_PATH . DS . trim(
                    str_replace('{LOCALE}', $langInfo['locale'], $this->_fileTemplates[$templateId]['filename']),
                    '/'
                );
                $langDir = dirname($langFilename);
                if (!file_exists($langDir)) {
                    mkdir($langDir, 0775, true);
                }
                $fileLangVar = $this->_fileTemplates[$templateId]['content'];
                $langFileData[$templateId] = array(
                    'dir' => $langDir,
                    'filename' => $langFilename,
                    'langVar' => $fileLangVar,
                    'fileContent' => '',
                );
                $langFileData[$templateId]['fileContent'] .= $this->_fileTemplates[$templateId]['header'];
            }

            // If we have no value, fill the var with the english/default text.
            $val = $entry['text'];
            if ($val == '') {
                $val = $english[$key]['text'];
            }
            // Put the lang var into the language file.
            $langFileData[$templateId]['fileContent'] .= str_replace(
                array('{KEY}', '{VALUE}'),
                array($key, $val),
                $langFileData[$templateId]['langVar']
            );
        }
        // Write footers, close the file handles and save changed filenames.
        foreach ($langFileData as $templateId => $langFile) {
            $fileContent = $langFile['fileContent'] . $this->_fileTemplates[$templateId]['footer'];
            $res = file_put_contents($langFile['filename'], $fileContent);
            chmod($langFile['filename'], 0664);
            $this->_changedFiles[] = $langFile['filename'];
        }
        return (($res !== false) && $res > 0) ? $res : false;
    }

    /**
     * Commit language file to svn repository
     *
     * @param string $language
     *
     * @return array
     */
    public function updateSvn($language)
    {
        $sFiles = implode(' ', $this->_changedFiles);
        $res = $this->_runSvnCommit('-m"' . sprintf($this->_commitMessageOneLanguage, $language) . '" ' . $sFiles);
        return $res;
    }

    /**
     * Commit all language files to svn repository
     *
     * @return array
     */
    public function updateSvnAll()
    {
        $res = $this->_runSvnCommit('-m"' . $this->_commitMessageAllLanguages . '"');
        return $res;
    }

    /**
     * Executes subversion commit and returns the content of the standard output and error stream.
     *
     * @param string $svnParams Additional SVN params.
     *
     * @return array
     */
    private function _runSvnCommit($svnParams = null)
    {
        $cmd = 'svn ci --username ' . $this->_svnUser . ' --password ' . $this->_svnPassword;
        if ($svnParams !== null) {
            $cmd .= " $svnParams";
        }

        $stdOut = '';
        $stdErr = '';
        $process = new Msd_Process($cmd, EXPORT_PATH);
        $process->execute();
        while ($process->isRunning()) {
            $stdOut .= $process->readOutput();
            $stdErr .=  $process->readError();
        }
        $process->close();
        return array(
            'output' => $stdOut,
            'error'  => $stdErr,
        );
    }
}

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
        $langInfo = $languageModel->getLanguageById($language);

        $languageEntriesModel = new Application_Model_LanguageEntries();
        $data = $languageEntriesModel->getLanguageKeys($language);
        $english = $languageEntriesModel->getLanguageKeys(2); // used as fallback for unmaintained vars

        $langMetaData = array();
        $res = 0;

        foreach ($data as $key => $entry) {
            $templateId = $entry['templateId'];
            // Did we have the meta data for the exported language file? If not, we will create it now.
            if (!isset($langMetaData[$templateId])) {
                $langFilename = EXPORT_PATH . DS . trim(
                    str_replace('{LOCALE}', $langInfo['locale'], $this->_fileTemplates[$templateId]['filename']),
                    '/'
                );
                $langDir = dirname($langFilename);
                if (!file_exists($langDir)) {
                    mkdir($langDir, 0775, true);
                }
                $fileLangVar = $this->_fileTemplates[$templateId]['content'];
                $fh = fopen($langFilename, "wb+");
                if (!$fh) {
                    return false;
                }
                $langMetaData[$templateId] = array(
                    'dir' => $langDir,
                    'filename' => $langFilename,
                    'fileHandle' => $fh,
                    'langVar' => $fileLangVar,
                );
                $res += (int) fwrite($fh, $this->_fileTemplates[$templateId]['header']);
            }
            // Get the meta data for the current template.
            $langMeta = $langMetaData[$templateId];

            // If we have no value, fill the var with the english/default text.
            $val = $entry['text'];
            if ($val == '') {
                $val = $english[$key]['text'];
            }
            // Put the lang var into the language file.
            $res += (int) fwrite(
                $langMeta['fileHandle'],
                str_replace(array('{KEY}', '{VALUE}'), array($key, $val), $langMeta['langVar'])
            );
        }
        // Write footers, close the file handles and save changed filenames.
        foreach ($langMetaData as $templateId => $langMeta) {
            $res += (int) fwrite($langMeta['fileHandle'], $this->_fileTemplates[$templateId]['footer']);
            fclose($langMeta['fileHandle']);
            chmod($langMeta['filename'], 0664);
            $this->_changedFiles[] = $langMeta['filename'];
        }
        return ($res > 0) ? $res : false;
    }

    /**
     * Commit language file to svn repository
     *
     * @return string
     */
    public function updateSvn($language)
    {
        $sFiles = implode(' ', $this->_changedFiles);
        $cmd = 'svn ci --username ' . $this->_svnUser . ' --password ' . $this->_svnPassword
                .' -m"' . sprintf($this->_commitMessageOneLanguage, $language) . '" '
               .' '.$sFiles;
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
               . EXPORT_PATH;
        $res = shell_exec($cmd);
        if (trim($res == '')) {
            $res = 'Nothing to update.';
        }
        return $res;
    }
}

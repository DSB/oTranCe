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
     *
     * @return array
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
        if ($langInfo['active'] != 1) {
            return false;
        }

        $languageEntriesModel = new Application_Model_LanguageEntries();
        $data                 = $languageEntriesModel->getLanguageKeys($language);
        $fallbackLanguage     = $languageEntriesModel->getLanguageKeys($fallbackLang);

        $langFileData = array();
        $res = array();
        $languageKeys = array_keys($fallbackLanguage);
        foreach ($languageKeys as $key) {
            $templateId = $fallbackLanguage[$key]['templateId'];
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
                $langFileData[$templateId]['fileContent'] .= $this->_fileTemplates[$templateId]['header'] . "\n";
            }

            // If we have no value, fill the var with the english/default text.
            $val = isset($data[$key]['text']) ? $data[$key]['text'] : '';
            if (trim($val) == '') {
                $val = $fallbackLanguage[$key]['text'];
            }
            // Put the lang var into the language file.
            $langFileData[$templateId]['fileContent'] .= str_replace(
                array('{KEY}', '{VALUE}'),
                array($key, $val),
                $langFileData[$templateId]['langVar']
            );
            $langFileData[$templateId]['fileContent'] .= "\n";
        }
        // Write footers, close the file handles and save changed filenames.
        $exportOk = true;
        foreach ($langFileData as $templateId => $langFile) {
            $fileContent = $langFile['fileContent'] . $this->_fileTemplates[$templateId]['footer'] . "\n";
            $size = file_put_contents($langFile['filename'], $fileContent);
            $exportOk = ($size !== false) && $exportOk;
            $res[$templateId]['size'] = $size;
            $res[$templateId]['filename'] = str_replace(EXPORT_PATH . DS, '', $langFile['filename']);
            chmod($langFile['filename'], 0664);
        }
        $res['exportOk'] = (count($res) > 0) && $exportOk;
        return $res;
    }
}

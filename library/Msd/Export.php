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
     * Array with file templates.
     * @var array
     */
    private $_fileTemplates = array();

    /**
     * Array with language meta infos (name, locale, ect.).
     * @var array
     */
    private $_langInfo = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        $fileTemplateModel = new Application_Model_FileTemplates();
        $this->_fileTemplates = $fileTemplateModel->getFileTemplatesAssoc();
    }

    /**
     * Get last change timestamp of a file
     *
     * @param string $language
     * @return int
     */
    public function getFileTimestamp($language)
    {
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
     * @param string $languageId
     *
     * @return array
     */
    public function exportLanguageFile($languageId)
    {
        $languageModel = new Application_Model_Languages();
        // retrieving fallback language,
        $fallbackLangId = $languageModel->getFallbackLanguage();
        // if the fallback language isn't set, detect id for "English" and use it instead
        if ($fallbackLangId === false) {
            $fallbackLangId = $languageModel->getLanguageIdFromLocale('en');
        }
        // mini cache - only read once per request
        if (!isset($this->_langInfo[$languageId])) {
            $this->_langInfo[$languageId] = $languageModel->getLanguageById($languageId);
        }
        if ($this->_langInfo[$languageId]['active'] != 1) {
            return false;
        }

        $languageEntriesModel = new Application_Model_LanguageEntries();
        $data                 = $languageEntriesModel->getLanguageKeys($languageId);
        $fallbackLanguage     = $languageEntriesModel->getLanguageKeys($fallbackLangId);
        $langFileData = array();
        $res = array();
        $languageKeys = array_keys($fallbackLanguage);
        foreach ($languageKeys as $key) {
            $templateId = $fallbackLanguage[$key]['templateId'];
            // Do we have the meta data for the exported language file? If not, we will create it now.
            if (!isset($langFileData[$templateId])) {
                $langFilename = EXPORT_PATH . DS . trim(
                    str_replace(
                        '{LOCALE}',
                        $this->_langInfo[$languageId]['locale'],
                        $this->_fileTemplates[$templateId]['filename']
                    ),
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
                    'langName' => $this->_langInfo[$languageId]['name'],
                    'langLocale' => $this->_langInfo[$languageId]['locale']
                );
                $langFileData[$templateId]['fileContent'] =
                    $this->_replaceLanguageMetaPlaceholder(
                        $this->_fileTemplates[$templateId]['header'],
                        $languageId
                    ) . "\n";
            }

            // If we have no value, fill the var with the english/default text.
            $val = isset($data[$key]['text']) ? $data[$key]['text'] : '';
            if (trim($val) == '') {
                $val = $fallbackLanguage[$key]['text'];
            }
            // Put the lang var into the language file.
            $langFileData[$templateId]['fileContent'] .= str_replace(
                array('{KEY}', '{VALUE}'),
                array($key, addslashes($val)),
                $langFileData[$templateId]['langVar']
            );
            $langFileData[$templateId]['fileContent'] .= "\n";
        }
        // Write footers, close the file handles and save changed filenames.
        $exportOk = true;
        foreach ($langFileData as $templateId => $langFile) {
            $fileFooter =
                $this->_replaceLanguageMetaPlaceholder(
                    $this->_fileTemplates[$templateId]['footer'],
                    $languageId
                );
            $langFile['fileContent'] .= $fileFooter . "\n";
            $size = file_put_contents($langFile['filename'], $langFile['fileContent']);
            $exportOk = ($size !== false) && $exportOk;
            $res[$templateId]['size'] = $size;
            $res[$templateId]['filename'] = str_replace(EXPORT_PATH . DS, '', $langFile['filename']);
            // Suppress warnings, if we can't change the file permissions.
            @chmod($langFile['filename'], 0664);
        }
        $res['exportOk'] = (count($res) > 0) && $exportOk;
        return $res;
    }

    /**
     * Replace meta placeholder of language.
     *
     * @param string $content    The content in which to search and replace
     * @param int    $languageId The Id of the language
     *
     * @return string
     */
    protected function _replaceLanguageMetaPlaceholder($content, $languageId)
    {
        $search = array(
            '{LANG_NAME}',
            '{LOCALE}'
        );

        $replace = array(
            $this->_langInfo[$languageId]['name'],
            $this->_langInfo[$languageId]['locale']
        );
        $res = str_replace($search, $replace, $content);
        return $res;
    }
}

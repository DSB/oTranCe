<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://www.otrance.org
 *
 * @package         oTranCe
 * @subpackage      Export
 * @version         SVN: $
 * @author          $Author$
 */
/**
 * Export
 *
 * @package         MySQLDumper
 * @subpackage      Export
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
     * Will hold all language keys grouped by template id
     * @var array
     */
    private $_keys;

    /**
     * Will hold all texts of the fallback language
     * @var array
     */
    private $_fallbackLanguageTranslations;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $fileTemplateModel = new Application_Model_FileTemplates();
        $this->_fileTemplates = $fileTemplateModel->getFileTemplatesAssoc();
    }

    /**
     * Get timestamp of the latest download package
     *
     *
     * @return int
     */
    public function getLatestDownloadPackageTimestamp()
    {
        $mTime = 0;
        $iterator = new DirectoryIterator(DOWNLOAD_PATH);
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isFile()) {
                continue;
            }
            $mTime = $fileinfo->getMTime();
        }
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
     * @param int $languageId
     *
     * @return array
     */
    public function exportLanguageFile($languageId)
    {
        $languageEntriesModel = new Application_Model_LanguageEntries();
        $languageModel = new Application_Model_Languages();
        // mini cache - only read once per request
        if (!isset($this->_langInfo[$languageId])) {
            $this->_langInfo[$languageId] = $languageModel->getLanguageById($languageId);
        }
        if ($this->_langInfo[$languageId]['active'] != 1) {
            // language is set to inactive - return and do nothing
            return false;
        }

        if ($this->_keys == null) {
            $this->_keys = $languageEntriesModel->getAllKeys();
        }
        // retrieving fallback language,
        $fallbackLangId = $languageModel->getFallbackLanguage();
        // if the fallback language isn't set, detect id for "English" and use it instead
        if ($fallbackLangId === false) {
            $fallbackLangId = $languageModel->getLanguageIdFromLocale('en');
        }

        if ($this->_fallbackLanguageTranslations == null) {
            $this->_fallbackLanguageTranslations = $languageEntriesModel->getTranslations($fallbackLangId);
        }

        $translations = $languageEntriesModel->getTranslations($languageId);

        foreach ($this->_keys as $key => $keyData) {
            $templateId = $keyData['templateId'];
            if($templateId == 0) die("jup");
            // Do we have the meta data for the exported language file? If not, we will create it now.
            if (!isset($langFileData[$templateId])) {
                $langFileData[$templateId] = $this->_getFileMetaData($languageId, $templateId);
            }

            $val = isset($translations[$key]) ? trim($translations[$key]) : '';
            if ($val == '') {
                // If we have no value, fill the var with the value of the fallback language.
                if (!empty($this->_fallbackLanguageTranslations[$key])) {
                    // if there is a translation in the fallback language, set it
                    $val = $this->_fallbackLanguageTranslations[$key];
                }
            }
            // Add content to template array
            $langFileData[$templateId]['fileContent'] .= str_replace(
                array('{KEY}', '{VALUE}'),
                array($keyData['key'], addslashes($val)),
                $langFileData[$templateId]['langVar']
            );
            $langFileData[$templateId]['fileContent'] .= "\n";
        }

        // Add footers and save file content to physical file
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
            // Suppress warnings, if we can't change the file permissions.
            @chmod($langFile['filename'], 0664);

            $res[$templateId]['size'] = $size;
            $res[$templateId]['filename'] = str_replace(EXPORT_PATH . DS, '', $langFile['filename']);
        }
        $res['exportOk'] = (count($res) > 0) && $exportOk;
        return $res;

    }

    /**
     * Extract meta data for a file and create directory if it doesn't exist
     *
     * @param int   $languageId   Id of language
     * @param int   $templateId   Id of template
     *
     * @return array
     */
    public function _getFileMetaData($languageId, $templateId)
    {
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
        $data = array(
            'dir' => $langDir,
            'filename' => $langFilename,
            'langVar' => $fileLangVar,
            'langName' => $this->_langInfo[$languageId]['name'],
            'langLocale' => $this->_langInfo[$languageId]['locale']
        );
        $data['fileContent'] =
                $this->_replaceLanguageMetaPlaceholder(
                    $this->_fileTemplates[$templateId]['header'],
                    $languageId
                ) . "\n";
        return $data;
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

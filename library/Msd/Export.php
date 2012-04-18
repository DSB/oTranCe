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
     * @var \Application_Model_FileTemplates
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
     * Will hold a list of translators grouped by languageId
     * @var array
     */
    private $_translatorList;

    /**
     * Will hold the project's configuration
     * @var array
     */
    private $_config;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $fileTemplateModel    = new Application_Model_FileTemplates();
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
     * Export a language from db to file
     *
     * @param int $languageId
     *
     * @return array
     */
    public function exportLanguageFile($languageId)
    {
        $languageEntriesModel = new Application_Model_LanguageEntries();
        $languageModel = new Application_Model_Languages();
        //mini cache - only read once per request
        if (!isset($this->_langInfo[$languageId])) {
            $this->_langInfo[$languageId] = $languageModel->getLanguageById($languageId);
        }
        if ($this->_langInfo[$languageId]['active'] != 1) {
            //language is set to inactive - return and do nothing
            return false;
        }

        if ($this->_keys == null) {
            $this->_keys = $languageEntriesModel->getAllKeys();
        }

        $this->_getFallbackLanguage($languageModel, $languageEntriesModel);
        $fileContent = $this->_addTranslations($languageId, $languageEntriesModel);

        //Add footers and save file content to physical file
        $exportOk = true;
        $res = array();
        foreach ($fileContent as $templateId => $langFile) {
            $fileFooter = $this->_replaceLanguageMetaPlaceholder(
                $this->_fileTemplates[$templateId]['footer'],
                $languageId
            );
            $langFile['fileContent'] .= $fileFooter . "\n";
            $size                     = file_put_contents($langFile['filename'], $langFile['fileContent']);
            $exportOk                 = ($size !== false) && $exportOk;
            //Suppress warnings, if we can't change the file permissions.
            @chmod($langFile['filename'], 0664);
            $res[$templateId]['size']     = $size;
            $res[$templateId]['filename'] = str_replace(EXPORT_PATH . '/', '', $langFile['filename']);
        }
        $res['exportOk'] = (count($res) > 0) && $exportOk;
        return $res;
    }

    /**
     * Detect fall back language and get translations
     *
     * @param Application_Model_Languages       $languageModel        The language model
     * @param Application_Model_LanguageEntries $languageEntriesModel The language entries model
     *
     * @return void
     */
    public function _getFallbackLanguage($languageModel, $languageEntriesModel)
    {
        //only read once per request
        if (!empty($this->_fallbackLanguageTranslations)) {
            return;
        }
        $fallbackLanguageId = $languageModel->getFallbackLanguage();
        // if the fallback language isn't set, detect id for "English" and use it instead
        if ($fallbackLanguageId == false) {
            $fallbackLanguageId = $languageModel->getLanguageIdFromLocale('en');
        }
        $this->_fallbackLanguageTranslations = $languageEntriesModel->getTranslations($fallbackLanguageId);
    }

    /**
     * Get translations and add them to file content array
     *
     * @param $languageId
     * @param $languageEntriesModel
     * @return array
     */
    public function _addTranslations($languageId, $languageEntriesModel)
    {
        $fileContent = array();
        $translations = $languageEntriesModel->getTranslations($languageId);

        if ($this->_config == null) {
            $config = Msd_Registry::getConfig();
            $this->_config = $config->getParam('project');
        }

        foreach ($this->_keys as $key => $keyData) {
            $templateId = $keyData['templateId'];
            //Do we have the meta data for the exported language file? If not, we will create it now.
            if (!isset($fileContent[$templateId])) {
                $fileContent[$templateId] = $this->_getFileMetaData($languageId, $templateId);
            }

            $val = isset($translations[$key]) ? trim($translations[$key]) : '';
            //If we have no value, fill the var with the value of the fallback language.
            if ($val == '' && (int) $this->_config['translateToFallback'] == 1) {
                if (isset($this->_fallbackLanguageTranslations[$key])) {
                    $val = $this->_fallbackLanguageTranslations[$key];
                }
            }

            //escape value depending on the delimiter
            if ($fileContent[$templateId]['delimiter'] == "'") {
                $val = str_replace("'", "\'", $val);
            } else {
                $val = str_replace('"', '\"', $val);
            }

            //Add content to template array
            $fileContent[$templateId]['fileContent'] .= str_replace(
                array('{KEY}', '{VALUE}'),
                array($keyData['key'], $val),
                $fileContent[$templateId]['langVar']
            );
            $fileContent[$templateId]['fileContent'] .= "\n";
        }
        return $fileContent;
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
        $langFilename = EXPORT_PATH . '/' . trim(
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
        $data = array(
            'dir'        => $langDir,
            'filename'   => $langFilename,
            'langVar'    => $this->_fileTemplates[$templateId]['content'],
            'langName'   => $this->_langInfo[$languageId]['name'],
            'langLocale' => $this->_langInfo[$languageId]['locale']
        );
        //Add file header
        $data['fileContent'] = $this->_replaceLanguageMetaPlaceholder(
            $this->_fileTemplates[$templateId]['header'],
            $languageId
        ) . "\n";

        //extract delimiter
        $pos = strpos($this->_fileTemplates[$templateId]['content'], '{VALUE}') + 7;
        $data['delimiter'] = substr($this->_fileTemplates[$templateId]['content'], $pos, 1);
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
        if ($this->_translatorList == null) {
            $userModel = new Application_Model_User();
            $this->_translatorList = $userModel->getTranslatorlist();
        }

        $search = array(
            '{LANG_NAME}',
            '{LOCALE}',
            '{TRANSLATOR_NAMES}'
        );
        $replace = array(
            $this->_langInfo[$languageId]['name'],
            $this->_langInfo[$languageId]['locale'],
            empty($this->_translatorList[$languageId]) ? '' : $this->_translatorList[$languageId],
        );
        $res = str_replace($search, $replace, $content);
        return $res;
    }
}

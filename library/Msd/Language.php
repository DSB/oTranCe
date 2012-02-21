<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Language
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Language class implemented as singleton
 *
 * Handles translation of language variables.
 *
 * @package         MySQLDumper
 * @subpackage      Language
 */
class Msd_Language
{
    /**
     * Instance
     *
     * @var Msd_Configuration
     */
    private static $_instance = NULL;

    /**
     * Holds the current language
     * @var string
     */
    private $_language;

    /**
     * Translator
     *
     * @var Zend_Translate
     */
    private $_translate = NULL;

    /**
     * Base directory for language files
     *
     * @var string
     */
    private $_baseLanguageDir = null;

    /**
     * Constructor loads the selected language of the user
     *
     * @return Msd_Language
     */
    private function __construct ()
    {
        $user              = new Application_Model_User();
        $this->_language = $user->loadSetting('interfaceLanguage', 'en');
        $this->loadLanguage($this->_language);
    }

    /**
     * Load new language.
     *
     * @param string $language New language
     *
     * @return void
     */
    public function loadLanguage($language)
    {
        if (empty($language)) {
            $language = 'en';
        }
        $this->_baseLanguageDir = APPLICATION_PATH . DS . 'language' . DS;
        $file = $this->_baseLanguageDir . $language . DS . 'lang.php';
        $translator = $this->getTranslator();
        if ($translator === null) {
            $translator = new Zend_Translate('array', $file, $language);
        } else {
            $translator->setAdapter(
                array(
                    'adapter' => 'array',
                    'content' => $file,
                    'locale' => $language
                )
            );
        }
        $this->setTranslator($translator);
        Zend_Registry::set('Zend_Translate', $translator);
    }
    /**
     * No cloning for singleton
     *
     * @return void
     */
    public function __clone()
    {
        throw new Msd_Exception('Cloning of Msd_Language is not allowed!');
    }

    /**
     * Magic getter to keep syntax in rest of script short
     *
     * @param mixed $property
     *
     * @return mixed
     */
    public function __get ($property)
    {
        $translated = $this->getTranslator()->_($property);
        if ($translated == $property && substr($property, 0, 2) == 'L_') {
            // no translation found -> remove prefix L_
            return substr($property, 2);
        }
        return $translated;
    }
    /**
     * Returns the single instance
     *
     * @return Msd_Language
     */
    public static function getInstance ()
    {
        if (NULL == self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Translate a Message from Zend_Validate.
     *
     * @param string $zendMessageId Message ID from Zend_Validate
     * @param string $messageText   Pre-translatet message
     *
     * @return string
     */
    public function translateZendId($zendMessageId, $messageText = '')
    {
        if (substr($zendMessageId, 0, 6) =='access' && $messageText > '') {
            // message is already translated by validator access
            return $messageText;
        }
        return $this->_translate->_(
            $this->_transformMessageId($zendMessageId)
        );
    }

    /**
     * Transforms a message ID in Zend_Validate format into Msd_Language format.
     *
     * @param string $zendMessageId Message ID from Zend_Validate
     *
     * @return string
     */
    private function _transformMessageId($zendMessageId)
    {
        $result = preg_replace('/([A-Z])/', '_${1}', $zendMessageId);
        $result = strtoupper($result);
        return 'L_ZEND_ID_' . $result;
    }

    /**
     * Get Translator
     *
     * @return Zend_Translate
     */
    public function getTranslator()
    {
        return $this->_translate;
    }

    /**
     * Set Translator
     *
     * @param Zend_Translate $translate
     *
     * @return void
     */
    public function setTranslator(Zend_Translate $translate)
    {
        $this->_translate = $translate;
    }

    /**
     * Retrieve a list of available languages.
     *
     * @return array
     */
    public function getAvailableLanguages()
    {
        $currentTranslator = $this->getTranslator();
        $languageDirs = glob(APPLICATION_PATH .'/language/*', GLOB_ONLYDIR);
        $ret = array();
        foreach ($languageDirs as $dir) {
            $parts = explode('/', $dir);
            $lang = array_pop($parts);
            $this->loadLanguage($lang);
            $translator = $this->getTranslator();
            $ret[$lang] = array('locale' => $lang, 'name' => $translator->translate('L_LANGUAGE_NAME'));
        }
        $this->setTranslator($currentTranslator);
        $this->loadLanguage($this->_language);
        return $ret;
    }
}

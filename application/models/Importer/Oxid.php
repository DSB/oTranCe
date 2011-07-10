<?php

class Application_Model_Importer_Oxid implements Msd_Import_Interface
{
    /**
     * Will hold detected and extracted data
     * @var array
     */
    private $_extractedData;

    /**
     * Array with keys we want to ignore
     * @var array
     */
    private $_ignoreKeys;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_ignoreKeys = array('charset');
        $this->_extractedData = array();
    }

    /**
     * Extract key value pairs from given string
     *
     * @param string $data The string to analyze
     *
     * @return array
     */
    public function extract($data)
    {
        // delete multi line comments
        $data = preg_replace('!/\*.*?\*/!s', '', $data);
        $data = explode("\n", $data);
        // 'KEY_1234' => "Text",
        $muster = "/^\\s*['\"](.*)['\"]\\s*=>\\s*['\"](.*)['\"],/s";
        //$muster2 = '/^(.*)\."(.*)"/'; -> Konkatenierte Zeilen

        $state = 0;
        $cnt = count($data);
        $line = '';
        for ($i = 0; $i < $cnt; $i++) {
            if ($state == 0) {
                // suche nächste Zeile mit einer Zuweisung "=>"
                WHILE (strpos($data[$i], '=>') === false && $i < $cnt-1) {
                    $i++;
                }
                $line = $this->_removeComment($data[$i]);
            } else {
                $line .= $this->_removeComment($data[$i]);
            }

            if (preg_match($muster, $line, $hit)) {
                $state = 0;
                $this->_extractedData[$hit[1]] = $hit[2];
            } else {
                // Muster hat nicht gematcht -> String konkatenieren bis "=>" vorhanden
                $state = 1;
            }
        }

        // Sonderbehandlung der letzten Zeile, weil nicht zwangsläufig ein Komma am Ende
        // stehen muss und so das bisherige Muster nicht matcht.
        // Suche vom Ende der Datei rückwärts bis zum => und baue einen String auf
        $i = $cnt - 1;
        $lastLine = '';
        while (strpos($lastLine, '=>') === false && $i >= 0) {
            $lastLine = $data[$i] . $lastLine;
            $i--;
        }
        // jetzt Muster ohne Komma darauf anwenden
        if (preg_match(str_replace(',/s', '/s', $muster), $lastLine, $hit)) {
            $this->_extractedData[$hit[1]] = $hit[2];
        }
        $this->_removeIgnoreKeys();
        return $this->_extractedData;
    }

    /**
     * Remove keys we want to ignore
     *
     * @return void
     */
    private function _removeIgnoreKeys() {
        foreach ($this->_ignoreKeys as $ignoreKey) {
            if (isset($this->_extractedData[$ignoreKey])) {
                unset($this->_extractedData[$ignoreKey]);
            }
        }
    }

    /**
     * Helper method to remove commetn lines satring with //
     *
     * @param string $val The string to clean from //-comment
     *
     * @return string The cleaned string
     */
    private function _removeComment($val)
    {
        if (substr(ltrim($val), 2) == '//') {
            $val = '';
        }
        return $val;
    }

}

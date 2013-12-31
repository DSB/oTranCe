<?php
/**
 * This file is part of oTranCe released under the GNU/GPL 2 license
 * http://otrance.org
 *
 * @package         oTranCe
 * @subpackage      Translate
 * @author          Daniel Schlichtholz <admin@mysqldumper.de>
 */
/**
 * Translation class
 *
 * @package         oTranCe
 * @subpackage      Translate
 */
class Otc_Translate
{

    /**
     * Get translation service instance
     *
     * @param string $translationServiceName Get translation service instance
     *
     * @return bool|Otc_Translate_Service_Abstract
     */
    public static function getInstance($translationServiceName = 'MyMemory')
    {
        // TODO read config and get correct translation service
        $translationServiceName = 'Otc_Translate_Service_' . $translationServiceName;

        try {
            $translationService = new $translationServiceName;
        } catch (Exception $e) {
            $translationService = false;
        }

        return $translationService;
    }
}

<?php
/**
 * This file is part of oTranCe released under the GNU GPL 3 license
 * http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Importer
 * @version         SVN: $
 * @author          $Author: $
 */

/**
 * Generic semicolon seperated file importer
 *
 * @package         oTranCe
 * @subpackage      Importer
 */

class Msd_Import_Ssv extends Msd_Import_Csv
{
    /**
     * Key -> Value separator
     * @var string
     */
    protected $_separator = ';';
}
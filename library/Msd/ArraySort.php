<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
 * http://www.mysqldumper.net
 *
 * @package         MySQLDumper
 * @subpackage      Sort
 * @author          Daniel Schlichtholz <admin@mysqldumper.de>
 */
/**
 * Class to handle sorting of multidimensional arrays
 *
 * @package         MySQLDumper
 * @subpackage      Sort
 */
class Msd_ArraySort
{
    /**
     * Sort a multidimensional assoc array by 1 or 2 fields (like ORDER BY in MySQL).
     *
     * @param array $arrayToSort The multidimensional array to sort
     * @param array $sortFields Array of fields to sort and the sort direction
     *                           array('fieldName1' => SORT_ASC, 'fieldName2' => SORT_DESC)
     *
     * @return array
     */
    public static function sortMultidimensionalArray($arrayToSort, $sortFields = array())
    {
        if (empty($sortFields)) {
            return $arrayToSort;
        }

        $sort     = array();
        $sortKeys = array_keys($sortFields);
        foreach ($arrayToSort as $key => $values) {
            foreach ($sortKeys as $field) {
                $sort[$field][$key] = $values[$field];
            }
        }

        switch (count($sortFields)) {
            case 2:
                array_multisort(
                    $sort[$sortKeys[0]], $sortFields[$sortKeys[0]],
                    $sort[$sortKeys[1]], $sortFields[$sortKeys[1]],
                    $arrayToSort
                );
                break;
            case 1:
                array_multisort($sort[$sortKeys[0]], $sortFields[$sortKeys[0]], $arrayToSort);
                break;
        }

        return $arrayToSort;
    }
}

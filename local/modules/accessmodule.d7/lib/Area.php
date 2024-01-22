<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Main;

use Accessmodule\Data\DataManager\AreaDataTable;

/**
 * TODO: работа с функционалом областей в процессе
 */
class Area
{
    /**
     * @param array $filter
     * @return array
     */
    public static function get(array $filter = array())
    {
        $res = AreaDataTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );
        while ($arArea = $res->fetch()) {
            $arAreas[] = $arArea;
        }

        return $arAreas;
    }
}
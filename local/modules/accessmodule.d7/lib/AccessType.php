<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Main;

use Accessmodule\Data\DataManager\AccessTypeDataTable;

class AccessType
{
    /**
     * @param array $filter
     * @return array
     */
    public static function get(array $filter = array())
    {
        $res = AccessTypeDataTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );
        while ($arAccessType = $res->fetch()) {
            $arAccessTypes[$arAccessType['ID']] = $arAccessType;
        }

        return $arAccessTypes;
    }
}
<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Data\Access;

use Accessmodule\Data\DataManager\RoleGroupDataTable;

class RoleGroup
{
    /**
     * @param array $filter
     * @return array
     */
    public static function get(array $filter = array())
    {
        $res = RoleGroupDataTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );
        while ($arGroup = $res->fetch()) {
            $arGroups[] = $arGroup;
        }

        return $arGroups;
    }

    /**
     * Возвращает массив связей Группа-роль по предоставленным группам
     *
     * @param array $arGroups
     * @return array
     */
    public static function getByGroups(array $arGroups = array()) {
        $res = RoleGroupDataTable::getList(
            array(
                'select' => array('*')
            )
        );
        while ($arRoleGroup = $res->fetch()) {
            if (array_search($arRoleGroup['GROUP_ID'], $arGroups)) {
                $arRoleGroups[] = $arRoleGroup;
            }
        }

        return $arRoleGroups;
    }

    /**
     * Добавляет в возвращаемые данные поле дополнительные поля
     *
     * @param array $arData
     * @return array
     */
    public static function includeAdditionalInfo(array $arData)
    {
        foreach ($arData as $arItem) {
            $arItem['NAME'] = self::getAdditionalInfo($arItem['GROUP_ID']);
            $arItems[] = $arItem;
        }
        return $arItems;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public static function getAdditionalInfo(int $id)
    {
        $res = \Bitrix\Main\GroupTable::GetById($id)->Fetch();
        $name = $res['NAME'];

        return $name;
    }

    /**
     * @param array $arFields
     * @return mixed
     */
    public static function add(array $arFields)
    {
        unset($arFields['ID']);
        $result = RoleGroupDataTable::add($arFields);

        return $result;
    }

    /**
     * @param int $id
     * @param array $arFields
     * @return mixed
     */
    public static function update(int $id, array $arFields)
    {
        $result = RoleGroupDataTable::update($id, $arFields);

        return $result;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public static function delete(int $id)
    {
        $result = RoleGroupDataTable::delete($id);

        return $result;
    }
}
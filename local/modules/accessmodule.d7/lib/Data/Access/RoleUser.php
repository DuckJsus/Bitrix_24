<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Data\Access;

use Accessmodule\Data\DataManager\RoleUserDataTable;

class RoleUser
{
    /**
     * @param array $filter
     * @return array
     */
    public static function get(array $filter = array())
    {
        $res = RoleUserDataTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );
        while ($arUser = $res->fetch()) {
            $arUsers[] = $arUser;
        }

        return $arUsers;
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
            $arItem['NAME'] = self::getAdditionalInfo($arItem['USER_ID']);
            $arItems[] = $arItem;
        }
        return $arItems;
    }

    /**
     * @param int $id
     * @return string
     */
    public static function getAdditionalInfo(int $id)
    {
        $res = \CUser::GetById($id)->Fetch();
        $name = $res['LAST_NAME'].' '.$res['NAME'];

        return $name;
    }

    /**
     * @param array $arFields
     * @return mixed
     */
    public static function add(array $arFields)
    {
        unset($arFields['ID']);
        $result = RoleUserDataTable::add($arFields);

        return $result;
    }

    /**
     * @param int $id
     * @param array $arFields
     * @return mixed
     */
    public static function update(int $id, array $arFields)
    {
        $result = RoleUserDataTable::update($id, $arFields);

        return $result;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public static function delete(int $id)
    {
        $result = RoleUserDataTable::delete($id);

        return $result;
    }
}
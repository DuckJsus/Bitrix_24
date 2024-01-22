<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Data\Access;

use Accessmodule\Data\DataManager\RoleDataTable;
use Accessmodule\Data\DB;

class Role
{
    /**
     * Возвращает массив всех ролей модуля
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function get(array $filter = array())
    {
        $res = RoleDataTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );
        while ($arRole = $res->fetch()) {
            $arRoles[$arRole['ID']] = $arRole;
        }

        return $arRoles;
    }

    /**
     * @param array $arFields
     * @return mixed
     */
    public static function add(array $arFields)
    {
        unset($arFields['ID']);
        $result = RoleDataTable::add($arFields);

        // Добавляет колонку соответствубщую роли в таблицы
        DB::addColumn('accessmodule_field', $arFields['NAME']);
        DB::addColumn('accessmodule_area', $arFields['NAME']);

        return $result;
    }

    /**
     * @param int $id
     * @param array $arFields
     * @return mixed
     */
    public static function update(int $id, array $arFields)
    {
        $result = RoleDataTable::update($id, $arFields);

        return $result;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public static function delete(int $id)
    {
        $role = self::get(['ID' => $id]);
        $role = array_shift($role);

        // Удаляет колонку соответствубщую роли из таблиц
        DB::deleteColumn('accessmodule_field', $role['NAME']);
        DB::deleteColumn('accessmodule_area', $role['NAME']);

        $result = RoleDataTable::delete($id);

        return $result;
    }
}
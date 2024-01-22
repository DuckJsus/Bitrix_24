<?php

namespace Accessmodule\Main;

use Accessmodule\Data\Access\Role;
use Accessmodule\Data\DB;
use Accessmodule\Data\Access\RoleGroup;
use Accessmodule\Data\Access\RoleUser;
use Accessmodule\Data\DataManager\AreaDataTable;
use Bitrix\Im\Log;

class Ajax
{
    /**
     * Классы для работы с ролями
     */
    private const ROLE_CLASSES = [
        'ROLES'     => 'Role',
        'GROUPS'    => 'RoleGroup',
        'USERS'     => 'RoleUser',
    ] ;

    /**
     * Возвращает массив для отрисовки страницы ролей
     *
     * @return array
     */
    public static function getRoles()
    {
        $arRoles = Role::get();
        $arRoleGroups = RoleGroup::get();
        $arRoleUsers = RoleUser::get();

        $arReturn = [
            'ROLES' => $arRoles,
            'GROUPS' => $arRoleGroups,
            'USERS' => $arRoleUsers,
        ];

        return $arReturn;
    }

    /**
     * Изменение записей в БД (роль, роль-пользователь, роль-группа)
     *
     * @param array $arAfter
     * @return array
     */
    public static function saveRoles(array $arAfter)
    {
        $arBefore = self::getRoles();

        $arMatchResult['ROLES']     = Main::matchArrays($arBefore['ROLES'], $arAfter['ROLES']);
        $arMatchResult['GROUPS']    = Main::matchArrays($arBefore['GROUPS'], $arAfter['GROUPS']);
        $arMatchResult['USERS']     = Main::matchArrays($arBefore['USERS'], $arAfter['USERS']);

        foreach ($arMatchResult as $entity => $arData) {
            $class = 'Accessmodule\\Data\\Access\\'.self::ROLE_CLASSES[$entity];
            foreach ($arData as $action => $arItems) {
                foreach ($arItems as $arItem) {
                    switch ($action){
                        case 'ADD':
                            $class::add($arItem);
                            //Добавить столбцы в поля и области
                            break;
                        case 'DELETE':
                            $class::delete($arItem['ID']);
                            //Добавить удаление столбцов из полей и областей
                            break;
                        case 'UPDATE':
                            $class::update($arItem['ID'], $arItem);
                    }
                }
            }
        }

        return self::getRoles();
    }

    /**
     * Возвращает массив для отрисовки страницы полей
     *
     * @return array
     */
    public static function getFields()
    {
        $arEntityTypes = EntityType::get();
        $arFields = DB::get('accessmodule_field');
        $arAccessTypes = AccessType::get();

        $arData = [
            'ENTITY_TYPES'  => $arEntityTypes,
            'FIELDS'        => $arFields,
            'ACCESS_TYPES'  => $arAccessTypes,
        ];

        return $arData;
    }

    /**
     * Изменяет записи в таблице полей
     *
     * @param array $arData
     * @return array
     */
    public static function saveFields(array $arData)
    {
        $arBefore = DB::get('accessmodule_field');
        $arAfter = $arData['FIELDS'];

        $arMatchResult = Main::matchArrays($arBefore, $arAfter);
        foreach ($arMatchResult['UPDATE'] as $arItem) {
            DB::update('accessmodule_field', $arItem['ID'], $arItem);
        }

        return self::getFields();
    }

    /**
     * Возвращает моссив для отрисовки страницы областей
     *
     * @return array
     */
    public static function getAreas()
    {
        $arAreas = DB::get('accessmodule_area');
        $arAccessTypes = AccessType::get();

        $arData = [
            'AREAS' => $arAreas,
            'ACCESS_TYPES' => $arAccessTypes,
        ];

        return $arData;
    }

    /**
     * Изменяет записи в таблице областей
     *
     * @param array $arData
     * @return array
     */
    public static function saveAreas(array $arData)
    {
        $arBefore = DB::get('accessmodule_area');
        $arAfter = $arData['AREAS'];

        $arMatchResult = Main::matchArrays($arBefore, $arAfter);

        foreach ($arMatchResult as $action => $arItems) {
            foreach ($arItems as $arItem) {
                switch ($action){
                    case 'ADD':
                        DB::add('accessmodule_area', $arItem);
                        break;
                    case 'DELETE':
                        AreaDataTable::delete($arItem['ID']);
                        break;
                    case 'UPDATE':
                        DB::update('accessmodule_area', $arItem['ID'], $arItem);
                }
            }
        }

        return self::getAreas();
    }

    /**
     * Обновляет все типы сущностей и поля в таблицах
     * Возвращает массив для отрисовки страницы ролей
     *
     * @return array
     */
    public static function reloadData()
    {
        EntityType::reloadEntityTypes();
        Field::reloadFields();

        return self::getRoles();
    }

    /**
     * Добавляет в возвращаемые данные поле дополнительные поля
     *
     * @param array $arData
     * @return array[]
     */
    public static function getAdditionalInfo(array $arData) {
        if ($arData['TYPE'] == 'group') {
            $name = RoleGroup::getAdditionalinfo($arData['ID']);
        } elseif ($arData['TYPE'] == 'user') {
            $name = RoleUser::getAdditionalinfo($arData['ID']);
        }
        $response = [
            'DATA' => [
                'NAME' => $name,
            ]
        ];

        return $response;
    }
}
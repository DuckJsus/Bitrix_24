<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Main;

use Accessmodule\Data\DataManager\FieldDataTable;
use Bitrix\Crm\Service;
use Accessmodule\Data\DB;

class Field
{
    /**
     * Возвращает массив полей модуля по фильтру (из БД модуля)
     *
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function get(array $filter = array())
    {
        $res = FieldDataTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );
        while ($arField = $res->Fetch()) {
            $arFields[] = $arField;
        }

        return $arFields;
    }

    /**
     * Возвращает список пользовательских полей (из БД сайта)
     *
     * @param int $entityTypeId
     * @return array
     */
    public static function getByEntityTypeD7(int $entityTypeId)
    {
        $factory = Service\Container::getInstance()->getFactory($entityTypeId);
        $fieldsCollection = $factory->getFieldsCollection();
        $arFields = $fieldsCollection->toArray();

        $arReturnFields = [];
        foreach ($arFields as $arField) {
            if (!empty($arField['USER_FIELD'])) {
                $arUserField = $arField['USER_FIELD'];

                $arReturnFields[] = [
                    'ID'                => $arUserField['ID'],
                    'NAME'              => $arUserField['FIELD_NAME'],
                    'LANG_NAME'         => $arUserField['EDIT_FORM_LABEL'],
                    'ENTITY_TYPE_ID'    => $entityTypeId,
                ];
            }
        }

        return $arReturnFields;
    }

    /**
     * Обновляет все типы сущностей и поля в таблицах
     *
     * @return void
     */
    public static function reloadFields()
    {
        $arEntityTypes = EntityType::get();
        $arBeforeFields = DB::getNotStandart('accessmodule_field');

        $arAfterFields = [];
        foreach ($arEntityTypes as $arEntityType) {
            $arAfterFields = array_merge($arAfterFields, self::getByEntityTypeD7($arEntityType['ID']));
        }

        $arMatchResult = Main::matchArrays($arBeforeFields, $arAfterFields);

        foreach ($arMatchResult as $action => $arItems) {
            foreach ($arItems as $arItem) {
                switch ($action){
                    case 'ADD':
                        DB::add('accessmodule_field', $arItem);
                        break;
                    case 'DELETE':
                        FieldDataTable::delete($arItem['ID']);
                        break;
                    case 'UPDATE':
                        DB::update('accessmodule_field', $arItem['ID'], $arItem);
                }
            }
        }
    }
}
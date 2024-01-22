<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Main;

use Bitrix\Crm\Service;
use Accessmodule\Data\DataManager\EntityTypeDataTable;

class EntityType
{
    /**
     * Массив ID основных типов сущностей
     */
    const STANDARD_ENTITY_TYPES = [
        \CCrmOwnerType::Lead,
        \CCrmOwnerType::Deal,
        \CCrmOwnerType::Contact,
        \CCrmOwnerType::Company,
    ];

    /**
     * Фильтр полей, подходяящих для модуля
     * При необходимости можно добавить фильтр
     *! Позже необходимо переделать под получение этих данных из настроек
     */
    const FIELD_FILTER = [
        'UF_',
        'ASSIGNED_BY_ID',
    ];

    /**
     * Обновляем строки в таблице accessmodule_entity_type
     *
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function reloadEntityTypes()
    {
        $arEntityTypes = self::get(['!STANDARD_TYPE' => true]);
        $arEntityTypesD7 = self::getDynamicEntityTypesD7();

        $arMatchResult = Main::matchArrays($arEntityTypes, $arEntityTypesD7);

        foreach ($arMatchResult as $action => $arItems) {
            foreach ($arItems as $arItem) {
                switch ($action){
                    case 'ADD':
                        EntityTypeDataTable::add($arItem);
                        break;
                    case 'DELETE':
                        EntityTypeDataTable::delete($arItem['ID']);
                        break;
                    case 'UPDATE':
                        EntityTypeDataTable::update($arItem['ID'], $arItem);
                }
            }
        }
    }

    /**
     * Получаем типы объектов из accessmodule_entity_type по фильтру
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function get(array $filter = array())
    {
        $arEntityTypes = [];

        $res = EntityTypeDataTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );
        while ($arEntityType = $res->fetch()) {
            $arEntityTypes[$arEntityType['ID']] = $arEntityType;
        }

        return $arEntityTypes;
    }

    /**
     * Возвращает массив ID смарт-процессов
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getDynamicEntityTypesD7()
    {
        $arEntityTypes = [];

        $container = Service\Container::getInstance();
        $typeDataClass = $container->getDynamicTypeDataClass();

        $listDynamicTypes = $typeDataClass::getList([
            'select' => ['*']
        ]);
        while($arListDynamicTypes = $listDynamicTypes->Fetch())
        {
            $arEntityTypes[] = [
                'ID' => $arListDynamicTypes['ENTITY_TYPE_ID'],
                'NAME' => $arListDynamicTypes['TITLE'],
            ];
        }

        return $arEntityTypes;
    }




    /**
     * Возвращает массив всех полей сайта, подходящих модулю
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getFields(): array
    {
        $arEntitiesTypeId = self::getEntities();
        $arEntitiesTypeId = array_merge(self::STANDARD_ENTITY_TYPES, $arEntitiesTypeId);

        $arEntitiesFields = [];

        foreach ($arEntitiesTypeId as $entityTypeId) {
            $arEntityFields = self::getEntityFields($entityTypeId);
            $arEntitiesFields[$entityTypeId][] = $arEntityFields;
        }

        return $arEntitiesFields;
    }

    /**
     * Возвращает массив полей сущности
     *
     * @param int $entityTypeId
     * @return array
     */
    public static function getEntityFields(int $entityTypeId): array
    {
        $factory = Service\Container::getInstance()->getFactory($entityTypeId);
        $fieldsCollection = $factory->getFieldsCollection();
        $arFields = $fieldsCollection->toArray();

        $entityTypeName = $factory->getEntityName();

        foreach ($arFields as $fieldId => $arField) {
            if (self::fieldsFilter($fieldId)) {
                $arReturnFields[$fieldId] = [
                    'FIELD_ID'          => $fieldId,
                    'LANG_NAME'         => $arField['LANG_NAME'],
                    'ENTITY_TYPE_ID'    => $entityTypeId,
                    'ENTITY_TYPE_NAME'  => $entityTypeName,
                ];
            }
        }

        return $arReturnFields;
    }

    /**
     * Возвращает true, если поле проходит по фильтру
     *
     * @param string $fieldId
     * @return bool|void
     */
    public static function fieldsFilter(string $fieldId)
    {
        foreach(self::FIELD_FILTER as $filter) {
            preg_match('/^'.$filter.'/', $fieldId, $match);
            if ($match) return true;
        }
    }
}
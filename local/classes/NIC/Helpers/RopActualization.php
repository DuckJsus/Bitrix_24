<?php
namespace NIC\Helpers;

use Bitrix\Crm\Service,
    NIC\Helpers\Logger,
    NIC\Helpers\GroupLeader as GroupLeaderHelper;

/**
 * Обновление поля РОП (руководитель сотрудника) в сущностях
 * Сущности и названия полей РОП задаются в отдельном инфоблоке
 *
 * @class RopActualization
 * @package NIC
 * @subpackage helpers
 * @author duckjsus
 * @email duckjsus@gmail.ru
 * @copyright ООО "НИЦ-ТЕХНОЛОГИИ" (c) 2023
 */
class RopActualization
{
    const IBLOCK_ROP_ACTUALIZATON_ID = 61;
    const REQUIRED_FIELDS = [
        "ENTITY_TYPE_ID"     =>  "ID Типа сущности",
        "ROP_FIELD_NAME"     =>  "Название поля РОП",
    ];

    public static function main(): bool
    {
        $arElements = self::getIblockElements(self::IBLOCK_ROP_ACTUALIZATON_ID);

        foreach ($arElements as $element) {
            self::actualization($element);
        }

        return true;
    }

    /**
     * Возвращает массив элементов инфоблока
     *
     * @param int $iblockId
     * @return array
     */
    private static function getIblockElements(int $iblockId): array
    {
        $arSelect = ["ID", "IBLOCK_ID", "PROPERTY_*"];
        $arFilter = ["IBLOCK_ID"=>$iblockId, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"];
        $res = \CIBlockElement::GetList([], $arFilter, false, ["nPageSize"=>50], $arSelect);
        while ($ob = $res->GetNextElement())
        {
            $arProperties = $ob->GetProperties();
            $arFields = $ob->GetFields();

            foreach (self::REQUIRED_FIELDS as $fieldName => $fieldTitle) {
                $arElements[$arFields["ID"]][$fieldName] = $arProperties[$fieldName]["VALUE"];
            }
        }

        return $arElements;
    }

    /**
     * Элемент - элемент инфоблока, содержащий информацию о crm сущости, в которой необходима актуализация РОП
     *
     * @param array $element
     * @return bool
     */
    private static function actualization(array $element): bool
    {
        $entityTypeId = $element["ENTITY_TYPE_ID"];
        $ropFieldName = $element["ROP_FIELD_NAME"];

        $factory = Service\Container::getInstance()->getFactory($entityTypeId);
        $parameters = [
            'select' => ["ID", "ASSIGNED_BY_ID", $ropFieldName],
            'filter' => []
        ];
        $items = $factory->getItems($parameters);
        
        $countAll = count($items);
        $countSuccess = 0;
        $countUnmatch = 0;
        foreach ($items as $item) {
            $assignedID = $item->getAssignedById();
            $ropID = GroupLeaderHelper::getUserGroupLeader($assignedID);

            if ($item->get($ropFieldName) != $ropID) {
                $countUnmatch++;

                $item->set($ropFieldName, $ropID);
                $result = $item->save();
                if ($result->isSuccess()){
                    $countSuccess++;
                }
            }

        }
        $counts = [
            "ENTITY_TYPE_ID" => $entityTypeId,
            "ALL" => $countAll,
            "UNMATCH" => $countUnmatch,
            "SUCCESS" => $countSuccess,
        ];
        Logger::otherLog('$counts', $counts);

        return true;
    }
}
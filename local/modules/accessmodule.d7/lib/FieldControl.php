<?php

namespace Accessmodule\Main;

use Accessmodule\Service\ServiceManager;
use Bitrix\Main\Loader;

Loader::IncludeModule('timeman');
Loader::includeModule('crm');

class FieldControl
{
    /**
     * Подключает подмену контейнера
     *
     * @return void
     */
    public static function includeServiceManager() {
        ServiceManager::addCustomCrmServices();
    }

    /**
     * Проверяет, что тип сущности поддерживается модулем
     *
     * @param int $entityTypeId
     * @return bool
     */
    public static function isSupportedEntityType(int $entityTypeId): bool
    {
        if ($entityTypeId === \CCrmOwnerType::Lead)
        {
            return true;
        }
        if ($entityTypeId === \CCrmOwnerType::Deal)
        {
            return true;
        }
        if ($entityTypeId === \CCrmOwnerType::Contact)
        {
            return true;
        }
        if ($entityTypeId === \CCrmOwnerType::Company)
        {
            return true;
        }
        if (\CCrmOwnerType::isUseDynamicTypeBasedApproach($entityTypeId))
        {
            return true;
        }

        return false;
    }
}
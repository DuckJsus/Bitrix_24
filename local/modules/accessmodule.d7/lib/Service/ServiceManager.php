<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Service;

use Bitrix\Main\DI\ServiceLocator;

class ServiceManager
{
    /**
     * Подмена контейнера
     *
     * @return void
     */
    public static function addCustomCrmServices(): void
    {
        ServiceLocator::getInstance()->addInstance('crm.service.container', new Container());
    }
}
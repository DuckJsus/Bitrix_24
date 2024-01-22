<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Service;

use Accessmodule\Main\FieldControl;
use Bitrix\Main\DI\ServiceLocator;

class Container extends \Bitrix\Crm\Service\Container
{
    /**
     * @param int $entityTypeId
     * @return \Bitrix\Crm\Service\Factory|null
     */
    public function getFactory(int $entityTypeId): ?\Bitrix\Crm\Service\Factory
    {
        if (FieldControl::isSupportedEntityType($entityTypeId))
        {
            $identifier = static::getIdentifierByClassName(Factory::class);
            if(!ServiceLocator::getInstance()->has($identifier))
            {
                $type = $this->getTypeByEntityTypeId($entityTypeId);
                if (!$type) return parent::getFactory($entityTypeId);

                $factory = new Factory($type);
                return $factory;

            }
            return ServiceLocator::getInstance()->get($identifier);
        }

        return parent::getFactory($entityTypeId);
    }
}
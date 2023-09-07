<?php

namespace NIC\Helpers\Sbis;

use Bitrix\Crm\Service,
    Bitrix\Main\Loader;
use NIC\Helpers\Logger;
use NIC\Helpers\Smart as SmartHelper;

Loader::includeModule('crm');

/**
 * Проверка существования/Добавление новых компаний из Тендера
 *
 * @class SbisCompany
 * @package NIC
 * @subpackage helpers
 * @author duckjsus
 * @email duckjsus@gmail.ru
 * @copyright ООО "НИЦ-ТЕХНОЛОГИИ" (c) 2023
 */
class SbisCompany
{
    private $innFieldName = '';
    private $kppFieldName = '';
    private $ogrnFieldName = '';

    private $name;
    private $fullName;
    private $inn;
    private $kpp;
    private $ogrn;
    private $assignedID;

    private $companyID;

    private $innOooLength   = 10; // длина ИНН у компаний ООО
    private $innIpLength    = 12; // длина ИНН у компаний ИП
    private $presetID; // Тип компании: 1 - ООО, 2 - ИП

    /**
     * SbisCompany constructor
     *
     * @param array $arReq Реквизиты компании
     */
    public function __construct(array $arReq, $assignedID = null)
    {
        $this->fullName = $arReq['full_name'];
        $this->name     = $arReq['name'];
        $this->inn      = $arReq['inn'];
        $this->kpp      = $arReq['kpp'];
        $this->ogrn     = $arReq['ogrn'];
        $this->assignedID = $assignedID;
    }

    /**
     * Возвращает ID компании (Находит существующую по ИНН+КПП или Создает новую)
     *
     * @return int
     */
    public function getCompanyID(): int
    {
        $this->checkCompany();
        $this->checkCompanyType();

        if (!$this->companyID) {
            $this->addCompany();
            $this->addRequisite();
        }

        return (int)$this->companyID;
    }

    /**
     * Ищет компанию по ИНН+КПП
     *
     * @return void
     */
    private function checkCompany()
    {
        $factory = Service\Container::getInstance()->getFactory(\CCrmOwnerType::Company);

        $arFilter = [
            'filter' => [
                $this->innFieldName => $this->inn,
                $this->kppFieldName => $this->kpp
            ]
        ];
        $arCompany = $factory->getItems($arFilter)[0];

        if (!empty($arCompany)) {
            $this->companyID = $arCompany->getId();
        }
    }

    /**
     * Определяет тип компании по длине ИНН (ООО или ИП)
     *
     * @return void
     */
    private function checkCompanyType()
    {
        if (mb_strlen($this->inn) == $this->innOooLength) {
            $this->presetID = 1;
        } elseif (mb_strlen($this->inn) == $this->innIpLength) {
            $this->presetID = 2;
        }
    }

    /**
     * Создает компанию
     *
     * @return void
     */
    private function addCompany()
    {
        if ($this->assignedID !== null) {
            $this->assignedID = SmartHelper::COMPANIES_KEEPER;
        }

        $arNewCompany = [
            'TITLE'                 => $this->name,
            'ASSIGNED_BY_ID'        => $this->assignedID,
            'OPENED'                => 'N', /*признак, что компанию могут видеть другие сотрудники с правами менеджера*/
            'COMPANY_TYPE'          => 'CUSTOMER',
            $this->innFieldName     => $this->inn,
            $this->kppFieldName     => $this->kpp,
            $this->ogrnFieldName    => $this->ogrn,
        ];

        $factory = Service\Container::getInstance()->getFactory(\CCrmOwnerType::Company);
        $item = $factory->createItem();
        $item->setFromCompatibleData($arNewCompany);
        $saveOperation = $factory->getAddOperation($item);
        $result = $saveOperation
            ->disableCheckAccess()
            ->disableCheckFields()
            ->launch();

        if ($result->isSuccess()) {
            $this->companyID = $result->getId();
            Logger::addLog('Добавлена компания. Data: ' . print_r($result->getData(), true), 'SBIS_COMPANY', 'SBIS_COMPANY');
        } else {
            Logger::addLog('Ошибка при добавлении компании. $arNewCompany: '.print_r($arNewCompany, true).'. Errors: ' . print_r($result->getErrorMessages(), true), 'SBIS_COMPANY_ERROR', 'SBIS_COMPANY_ERROR');
        }
    }

    /**
     * Создает реквизиты компании
     *
     * @return void
     */
    private function addRequisite()
    {
        $arRequisiteFields = [
            'ENTITY_TYPE_ID'        => \CCrmOwnerType::Company, /*реквизит для компании*/
            'ENTITY_ID'             => $this->companyID, /* ид нашей созданной компании*/
            'NAME'                  => $this->name,
            'PRESET_ID'             => $this->presetID,
            'RQ_NAME'               => $this->name,
            'RQ_COMPANY_NAME'       => $this->name,
            'RQ_COMPANY_FULL_NAME'  => $this->fullName,
            'RQ_INN'                => $this->inn,
            'RQ_KPP'                => $this->kpp,
            'RQ_OGRN'               => $this->ogrn,
        ];

        $requisiteEntity = new \Bitrix\Crm\EntityRequisite();
        $requisiteEntity->add($arRequisiteFields);
    }
}
<?php

namespace NIC\Helpers\Sbis;

use Bitrix\Crm\Service,
    Bitrix\Main\Loader;
use NIC\Helpers\Logger;
use NIC\Helpers\Smart;
use NIC\Helpers\UserRotation;

Loader::includeModule('crm');

/**
 * Добавление нового лида ОГЗ из Тендера
 *
 * @class SbisTender
 * @package NIC
 * @subpackage helpers
 * @author duckjsus
 * @email duckjsus@gmail.ru
 * @copyright ООО "НИЦ-ТЕХНОЛОГИИ" (c) 2023
 */
class SbisTender
{
    private $folderName;
    private $stageID;
    private $categoryID;
    private $arReceivingTender;
    private $arTender;
    private $arInitiator;
    private $arOrganizer;
    private $performerFromOgz;
    // Реквизиты Заказчика
    private $arInitiatorFields = [
        'initiator_name',
        'initiator_full_name',
        'initiator_inn',
        'initiator_kpp',
        'initiator_ogrn',
    ];
    // Реквизиты Организатора
    private $arOrganizerFields = [
        'organizer_name',
        'organizer_full_name',
        'organizer_inn',
        'organizer_kpp',
        'organizer_ogrn',
    ];

    /**
     * SbisTender construct
     *
     * @param array $arReceivingTender
     * @param string $folderName
     */
    public function __construct(array $arReceivingTender, string $folderName, $performerFromOgz)
    {
        $this->folderName = $folderName;
        $this->arReceivingTender = $arReceivingTender;
        $this->setSmartProcessStage();
        $this->performerFromOgz = $performerFromOgz;
        $this->setTender();
    }

    /**
     * Добавление Лида ОГЗ
     *
     * @return void
     */
    public function addTender()
    {
        $factory = Service\Container::getInstance()->getFactory(OGZ_LEAD_ENTITY_TYPE_ID);
        $item = $factory->createItem();
        $item->setFromCompatibleData($this->arTender);
        $saveOperation = $factory->getAddOperation($item);
        $result = $saveOperation
            ->disableCheckAccess()
            ->disableCheckFields()
            ->launch();

        if ($result->isSuccess()) {
            Logger::addLog('Добавлен элемент смарт-процесса ID Процесса=144. Data: ' . print_r($result->getData(), true), 'SBIS_TENDER', 'SBIS_TENDER');
        } else {
            Logger::addLog('Ошибка при добавлении элемента смарт-процесса. Errors: ' . print_r($result->getErrorMessages(), true), 'SBIS_TENDER_ERROR', 'SBIS_TENDER_ERROR');
        }
    }

    /**
     * Задает стандартизированный массив полей реквизитов из реквизитов заказчика
     *
     * @return void
     */
    private function setInitiatorArray()
    {
        foreach ($this->arInitiatorFields as $field) {
            $newField = str_replace('initiator_', '', $field);
            $this->arInitiator[$newField] = $this->arReceivingTender[$field];
        }
    }

    /**
     * Задает стандартизированный массив полей реквизитов из реквизитов организатора
     *
     * @return void
     */
    private function setOrganizerArray()
    {
        foreach ($this->arOrganizerFields as $field) {
            $newField = str_replace('organizer_', '', $field);
            $this->arOrganizer[$newField] = $this->arReceivingTender[$field];
        }
    }

    /**
     * Находит/создает компанию заказчика, возвращает ID
     *
     * @return int
     */
    private function setInitiator(): int
    {
        $this->setInitiatorArray();
        $obSbisCompany = new SbisCompany($this->arInitiator, Smart::CUSTOMER_KEEPER);
        $initiatorID = $obSbisCompany->getCompanyID();

        return $initiatorID;
    }

    /**
     * Находит/создает компанию организатора, возвращает ID
     *
     * @return int
     */
    private function setOrganizer()
    {
        $this->setOrganizerArray();
        $obSbisCompany = new SbisCompany($this->arOrganizer);
        $organizerID = $obSbisCompany->getCompanyID();

        return $organizerID;
    }

    /**
     * Задает массив полей Лида ОГЗ
     *
     * @return void
     */
    private function setTender()
    {
        $this->arTender = [
            'ASSIGNED_BY_ID'                        => Smart::OGZ_HEAD,
            'UF_CRM_36_PERFORMER_FROM_OGZ'          => (int)$this->performerFromOgz,
            'UF_CRM_36_INITIATOR'                   => $this->setInitiator(),
            'UF_CRM_36_ORGANIZER'                   => $this->setOrganizer(),
            'UF_CRM_36_REQUEST_RECEIVING_END_DATE'  => $this->setReceivingEndDate(),
            'UF_CRM_36_PUBLISH_DATE'                => $this->setPublishDate(),
            'UF_CRM_36_INITIAL_MAX_PRICE'           => $this->setPrice(),
            'UF_CRM_36_TENDER_SBIS_URL'             => $this->arReceivingTender['tender_sbis_url'],
            'UF_CRM_36_NAME'                        => $this->arReceivingTender['name'],
            'UF_CRM_36_REGION'                      => $this->arReceivingTender['region'],
            'UF_CRM_36_NUMBER'                      => (int)$this->arReceivingTender['number'],
            'STAGE_ID'                              => $this->stageID,
            'CATEGORY_ID'                           => $this->categoryID,
        ];
        Logger::addLog('Задан массив полей лида. arTender: ' . print_r($this->arTender, true), 'SBIS_TENDER', 'SBIS_TENDER');
    }

    /**
     * Возвращает дату/время в формате битрикса
     *
     * @param string $date
     * @return string
     */
    private function setReceivingEndDate()
    {
        $date = $this->arReceivingTender['request_receiving_end_date'];
        if ($date !== null) {
            $obDate = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
            return $obDate->format("d.m.Y H:i:s");
        }
    }

    /**
     * Возвращает дату в формате битрикса
     *
     * @param string $date
     * @return string
     */
    private function setPublishDate()
    {
        $date = $this->arReceivingTender['publish_date'];
        if ($date !== null) {
            $obDate = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
            return $obDate->format("d.m.Y");
        }
    }

    private function setPrice()
    {
        if ($this->arReceivingTender['price'] !== null) {
            return $this->arReceivingTender['price'].'|RUB';
        } else {
            return null;
        }
    }

    /**
     * Задает начальную стадию в зависимости от папки тендеров
     * Каждая стадия принаджелит к отдельной воронке
     *
     * @return void
     */
    private function setSmartProcessStage()
    {
        switch ($this->folderName) {
            case 'Закупки 1':
                $this->stageID = 'DT144_48:NEW';
                $this->categoryID = 48;
                break;
            case 'Закупки 2':
                $this->stageID = 'DT144_51:NEW';
                $this->categoryID = 51;
                break;
            case 'Закупки 3':
                $this->stageID = 'DT144_52:NEW';
                $this->categoryID = 52;
                break;
        }
    }
}
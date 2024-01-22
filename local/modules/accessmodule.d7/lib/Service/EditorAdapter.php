<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Service;

use Accessmodule\Internals\EditorConfig\Config;
use Accessmodule\Service\EntityEditor\FieldManager;
use Bitrix\Crm\EO_Status_Collection;
use Bitrix\Crm\Field;
use Bitrix\Crm\Item;
use CCrmFieldInfoAttr;
use Bitrix\Crm\Service\Context;

class EditorAdapter extends \Bitrix\Crm\Service\EditorAdapter
{
    protected static  ?Field\Collection $staticFieldsCollection = null;
    protected int     $entityTypeId;
    protected Context $crmContext;
    protected int $typeId;

    /**
     * @param \Bitrix\Crm\Field\Collection $fieldsCollection
     * @param array $dependantFieldsMap
     * @throws \Exception
     */
    public function __construct(Field\Collection $fieldsCollection, array $dependantFieldsMap = [])
    {
        static::$staticFieldsCollection = $fieldsCollection;
        parent::__construct($fieldsCollection, $dependantFieldsMap);
    }

    /**
     * @param int $entityTypeId
     * @return $this
     */
    public function setEntityTypeId(int $entityTypeId): static
    {
        $this->entityTypeId = $entityTypeId;
        return $this;
    }

    /**
     * @param int $typeId
     * @return $this
     */
    public function setTypeId(int $typeId): static
    {
        $this->typeId = $typeId;
        return $this;
    }

    /**
     * @param \Vendor\Project\Dynamic\Service\Context $crmContext
     * @return $this
     */
    public function setCrmContext(Context $crmContext): static
    {
        $this->crmContext = $crmContext;
        return $this;
    }

    /**
     * Добавление свойства NotDisplayed полям в fieldsCollection
     *
     * @param Config $config
     * @return void
     */
    protected function markHiddenFields(Config $config): void
    {
        if (!is_null($config))
        {
            $hiddenFields = $config->getHiddenFields();

            foreach ($this->fieldsCollection as $field)
            {
                if (in_array($field->getName(), $hiddenFields))
                {
                    $field->setAttributes(
                        array_unique(array_merge($field->getAttributes(), [CCrmFieldInfoAttr::NotDisplayed]))
                    );
                }
            }
        }
    }

    /**
     * @param Item $item
     * @param EO_Status_Collection $stages
     * @param array $componentParameters
     * @return EditorAdapter
     */
    public function processByItem(Item $item, EO_Status_Collection $stages, array $componentParameters = []): EditorAdapter
    {
        if ($item->getId()) {
            $fieldsConfig = new Config($item);
            $this->markHiddenFields($fieldsConfig);
        }

        return parent::processByItem($item, $stages, $componentParameters);
    }

    /**
     * @param string $fieldCaption
     * @return void
     */
    public function addClientField(string $fieldCaption): void
    {
        $this->addEntityField(
            static::getClientField(
                $fieldCaption,
                static::FIELD_CLIENT,
                static::FIELD_CLIENT_DATA_NAME,
                ['entityTypeId' => $this->entityTypeId]
            )
        );
    }

    /**
     * @param string $fieldCaption
     * @param bool $isPaymentsEnabled
     * @return void
     */
    public function addOpportunityField(string $fieldCaption, bool $isPaymentsEnabled): void
    {
        $this->addEntityField(
            static::getOpportunityField($fieldCaption, static::FIELD_OPPORTUNITY, $isPaymentsEnabled)
        );
    }

    /**
     * @param string $fieldCaption
     * @return void
     */
    public function addProductRowSummaryField(string $fieldCaption): void
    {
        $this->addEntityField(static::getProductRowSummaryField($fieldCaption));
    }
}
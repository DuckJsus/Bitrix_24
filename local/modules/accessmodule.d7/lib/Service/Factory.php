<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Service;

use Accessmodule\Main\FieldControl;
use Bitrix\Crm\Item as BaseItem;
use Bitrix\Crm\Model\Dynamic\Type;
use Bitrix\Crm\Service\Context;
use Bitrix\Crm\Service\Factory\Dynamic as DynamicFactory;
use Bitrix\Crm\Service\Operation;
use Bitrix\Main\Result;

class Factory extends DynamicFactory
{
    /**
     * @return \Bitrix\Crm\Service\EditorAdapter
     * @throws \Exception
     */
    public function getEditorAdapter(): \Bitrix\Crm\Service\EditorAdapter
    {
        if (!$this->editorAdapter)
            {
            $this->editorAdapter = new EditorAdapter($this->getFieldsCollection(), $this->getDependantFieldsMap());

            $this->editorAdapter
                ->setTypeId($this->getType()->getId())
                ->setEntityTypeId($this->getEntityTypeId())
                ->setCrmContext(Container::getInstance()->getContext());

            if ($this->isClientEnabled())
            {
                $this->editorAdapter->addClientField($this->getFieldCaption(EditorAdapter::FIELD_CLIENT));
            }
            if ($this->isLinkWithProductsEnabled())
            {
                $this->editorAdapter->addOpportunityField(
                    $this->getFieldCaption(EditorAdapter::FIELD_OPPORTUNITY),
                    $this->isPaymentsEnabled()
                );
                $this->editorAdapter->addProductRowSummaryField(
                    $this->getFieldCaption(EditorAdapter::FIELD_PRODUCT_ROW_SUMMARY)
                );
            }
        }

        return $this->editorAdapter;
    }
}
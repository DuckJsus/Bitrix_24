<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Internals\EditorConfig;

use Accessmodule\Data\Access\Access;
use Accessmodule\Internals\Contract\IEditorConfig;
use Bitrix\Crm\Item;

class Config
{
    protected array $fieldsCollection = [];

    protected int $userId;
    protected int $itemId;
    protected int $entityTypeId;
    protected int $assignedById;

    /**
     * @param Item $item
     */
    public function __construct(Item $item)
    {
        global $USER;
        $this->userId = $USER->GetID();
        $this->itemId = $item->getId();
        $this->entityTypeId = $item->getEntityTypeId();
        $this->assignedById = $item->getAssignedById();
        $this->createFieldsCollection();
    }

    /**
     * @return void
     */
    private function createFieldsCollection()
    {
        $obAccess = new Access($this->entityTypeId, $this->userId, $this->assignedById);
        $this->fieldsCollection = $obAccess->getFieldsAccess();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        $field = $this->fieldsCollection[$name];

        return $field;
    }

    /**
     * Возвращает массив полей, к просмотру которых нет доступа
     *
     * @return array
     */
    public function getHiddenFields(): array
    {
        $arHiddenFields = [];
        foreach ($this->fieldsCollection as $field) {
            if ($field['HIDDEN']) {
                $arHiddenFields[] = $field['NAME'];
            }
        }

        return $arHiddenFields;
    }
}
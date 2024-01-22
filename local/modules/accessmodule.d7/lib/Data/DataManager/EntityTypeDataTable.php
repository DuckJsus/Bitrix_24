<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Data\DataManager;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\TextField,
    Bitrix\Main\Type\DateTime;
use Bitrix\Main\ORM\Fields\BooleanField;

Loc::loadMessages(__FILE__);


class EntityTypeDataTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'accessmodule_entity_type';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'required' => true,
                    'title' => Loc::getMessage('DATA_ID_FIELD')
                ]
            ),
            new TextField(
                'NAME',
                [
                    'required' => true,
                    'title' => Loc::getMessage('DATA_NAME_FIELD')
                ]
            ),
            new BooleanField(
                'STANDARD_TYPE',
                [
                    'default' => false,
                    'title' => Loc::getMessage('DATA_STANDARD_TYPE_FIELD')
                ]
            ),
        ];
    }
}


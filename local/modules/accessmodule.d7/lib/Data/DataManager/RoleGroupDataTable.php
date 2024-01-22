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

Loc::loadMessages(__FILE__);


class RoleGroupDataTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'accessmodule_role_group';
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
                    'autocomplete' => true,
                    'title' => Loc::getMessage('DATA_ID_FIELD')
                ]
            ),
            new IntegerField(
                'ROLE_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('DATA_ROLE_ID_FIELD')
                ]
            ),
            new IntegerField(
                'GROUP_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('DATA_GROUP_ID_FIELD')
                ]
            ),
        ];
    }
}


<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Data;

/**
 * Класс для работы напрямую с БД
 * Необходим из-за добавления столбцов в таблицы в процессе работы
 */
class DB
{
    /**
     * @param string $table
     * @param string $columnName
     * @return bool
     */
    public static function addColumn(string $table, string $columnName)
    {
        global $DB;

        if ($DB->TableExists($table) && !self::fieldExist($table, $columnName))
        {
            $res = $DB->DDL("ALTER TABLE ".$table." ADD COLUMN ".$columnName." INT(11) DEFAULT 1;");
            return $res;
        }
        return true;
    }

    /**
     * @param string $table
     * @param string $columnName
     * @return bool
     */
    public static function deleteColumn(string $table, string $columnName)
    {
        global $DB;

        if ($DB->TableExists($table) && self::fieldExist($table, $columnName))
        {
            $res = $DB->DDL("ALTER TABLE ".$table." DROP COLUMN ".$columnName.";");
            return $res;
        }
        return true;
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     * @return bool
     */
    public static function fieldExist(string $tableName, string $fieldName): bool
    {
        global $DB;

        return ($DB->DDL("SHOW COLUMNS FROM ".$tableName." LIKE '".$fieldName."';")->Fetch()) ? true : false;
    }

    /**
     * @param string $table
     * @return array|bool
     */
    public static function get(string $table)
    {
        global $DB;

        if ($DB->TableExists($table))
        {
            $res = $DB->DDL("SELECT * FROM ".$table.";");
            while ($arRes = $res->Fetch()) {
                $arResult[] = $arRes;
            }
            return $arResult;
        }
        return true;
    }

    /**
     * @param string $table
     * @param int $entityTypeId
     * @return array|bool
     */
    public static function getByEntityTypeId(string $table, int $entityTypeId)
    {
        global $DB;

        if ($DB->TableExists($table))
        {
            $res = $DB->DDL("SELECT * FROM ".$table." WHERE ENTITY_TYPE_ID = ".$entityTypeId.";");
            while ($arRes = $res->Fetch()) {
                $arResult[] = $arRes;
            }
            return $arResult;
        }
        return true;
    }

    /**
     * Получение стандатных полей (Ответственный) / сущностей (Не смарт процессы)
     *
     * @param string $table
     * @return array|bool
     */
    public static function getNotStandart(string $table)
    {
        global $DB;

        if ($DB->TableExists($table))
        {
            $res = $DB->DDL("SELECT * FROM ".$table." WHERE STANDARD_TYPE != true;");
            while ($arRes = $res->Fetch()) {
                $arResult[] = $arRes;
            }
            return $arResult;
        }
        return true;
    }

    /**
     * @param string $table
     * @param array $arFields
     * @return bool|void
     */
    public static function add(string $table, array $arFields)
    {
        global $DB;

        if ($DB->TableExists($table))
        {
            $fields = '';
            foreach ($arFields as $key => $value) {
                if (!empty($fields)) {
                    $fields .= ', ';
                }
                if (is_string($value)) {
                    $value = "'".$value."'";
                }
                $fields .= $key.'='.$value;
            }
            $DB->DDL("INSERT INTO ".$table." SET ".$fields.";");
            return true;
        }
    }

    /**
     * @param string $table
     * @param int $id
     * @param array $arFields
     * @return bool|void
     */
    public static function update(string $table, int $id, array $arFields)
    {
        unset($arFields['ID']);
        global $DB;

        if ($DB->TableExists($table))
        {
            $fields = '';
            foreach ($arFields as $key => $value) {
                if (!empty($fields)) {
                    $fields .= ', ';
                }
                if (is_string($value)) {
                    $value = "'".$value."'";
                }
                $fields .= $key.'='.$value;
            }
            $DB->DDL("UPDATE ".$table." SET ".$fields." WHERE ID=".$id.";");
            return true;
        }
    }
}
<?php
/**
 * ==================================================
 * Developer: Alexander Derevyanko
 * E-mail: adsergich@gmail.com
 * ==================================================
 */
namespace Accessmodule\Main;

use Bitrix\Main\Page\Asset;
use Accessmodule\Data\Access\RoleUser;
use Accessmodule\Data\Access\RoleGroup;

class Main
{
    /**
     * Возвращаем массив с ключами ADD, DELETE, UPDATE
     * Для работы обязателен ключ ID в массиве
     *
     * @param array $arBefore
     * @param array $arAfter
     * @return array
     */
    public static function matchArrays($arBefore, $arAfter)
    {
        if (!is_array($arBefore) && is_array($arAfter)) {
            $arMatchResult['ADD'] = $arAfter;

        } elseif (is_array($arBefore) && !is_array($arAfter)) {
            $arMatchResult['DELETE'] = $arBefore;

        } elseif (is_array($arBefore) && is_array($arAfter)) {
            foreach ($arAfter as $aItem) {
                $arBItem = self::arraySearch($arBefore, 'ID', $aItem['ID']);

                if (is_array($arBItem)) {
                    $bId = array_keys($arBItem)[0];
                    $bItem = array_values($arBItem)[0];
                } else {
                    $bId = false;
                    $bItem = false;
                }

                if (empty($bItem)) {
                    $arMatchResult['ADD'][] = $aItem;

                } elseif ($aItem !== $bItem) {
                    $arMatchResult['UPDATE'][] = $aItem;
                }
                unset($arBefore[$bId]);
            }
            if (!empty($arBefore)) {
                $arMatchResult['DELETE'] = $arBefore;
            }
        }

        return $arMatchResult;
    }

    /**
     * Добавляет в возвращаемые данные поле дополнительные поля
     *
     * @param array $arData
     * @return array
     */
    public static function includeAdditionalInfo(array $arData)
    {
        $arData['GROUPS'] = RoleGroup::includeAdditionalInfo($arData['GROUPS']);
        $arData['USERS'] = RoleUser::includeAdditionalInfo($arData['USERS']);
        return $arData;
    }

    /**
     * Возвращает первый элемент массива с совпадение ключ => значение
     *
     * @param array $array
     * @param $key
     * @param $value
     * @return array|null
     */
    private static function arraySearch(array $array, $key, $value)
    {
        foreach ($array as $id => $item) {
            foreach ($item as $k => $v) {
                if ($key === $k && $value === $v) {
                    return [$id => $item];
                }
            }
        }
        return null;
    }

    /**
     * Переводит все ключи массива в верхний регистр
     *
     * @param $arr
     * @return array|array[]|void|void[]
     */
    public static function array_change_key_case_upper($arr)
    {
        if (is_array($arr)) {
            return array_map(function($item){
                if(is_array($item))
                    $item = self::array_change_key_case_upper($item);
                return $item;
            },array_change_key_case($arr, CASE_UPPER));
        }
    }

    /**
     * Переводит все ключи массива в нижний регистр
     *
     * @param $arr
     * @return array|array[]|void|void[]
     */
    public static function array_change_key_case_lower($arr)
    {
        if (is_array($arr)) {
            return array_map(function($item){
                if(is_array($item))
                    $item = self::array_change_key_case_lower($item);
                return $item;
            },array_change_key_case($arr, CASE_LOWER));
        }
    }
}

<?php

namespace NIC\Helpers;

use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;

class Time
{
    public const FRIDAY = 5;
    public const SATURDAY = 6;
    public const SUNDAY = 0;

    public const WEEK_WORKDAYS = 5;
    public const WORKDAY_START_HOUR = 8;
    public const WORKDAY_END_HOUR = 17;
    public const WORKDAY_LENGHT = self::WORKDAY_END_HOUR - self::WORKDAY_START_HOUR;

    /**
     * Добавляет количество рабочих дней к дате
     * Функция для строк
     * @param string $startDate
     * @param int $amountDays
     * @return string
     */
    public static function strAddWorkDays(string $startDate, int $amountDays): string
    {
        $obStartDate = \DateTime::createFromFormat('d.m.Y H:i:s', $startDate);
        $obEndDate = $obStartDate;

        // проверяет, что день рабочий и прибавляет заданное количество РАБОЧИХ дней
        for ($i = 0; $i < $amountDays;) {
            $obEndDate->modify("+ 1 day");

            if (!self::isWorkDay($obEndDate)) {
                continue;
            }

            $i++;
        }

        return $obEndDate->format('d.m.Y H:i:s');
    }

    /**
     * Добавляет количество рабочих дней к дате
     * Функция для объектов
     * @param string $obStartDate
     * @param int $amountDays
     * @return string
     */
    public static function objAddWorkDays(\DateTime $obStartDate, int $amountDays): \DateTime
    {
        $obEndDate = $obStartDate;

        // проверяет, что день рабочий и прибавляет заданное количество РАБОЧИХ дней
        for ($i = 0; $i < $amountDays;) {
            $obEndDate->modify("+ 1 day");

            if (!self::isWorkDay($obEndDate)) {
                continue;
            }

            $i++;
        }

        return $obEndDate;
    }

    /**
     * Возвращает true - если текущий день в календаре - рабочий
     * @return bool
     */
    public static function isWorkDay($obDateTime):bool
    {
        if (!Loader::IncludeModule('calendar'))
            die();

        $arCalendar = \CCalendar::GetSettings();
        $arWorkDays = explode(",", $arCalendar['year_workdays']);
        $arHolidays = explode(",", $arCalendar['year_holidays']);
        $todayFormatted = ltrim($obDateTime->format('d.m'), '0');

        if ($obDateTime->format("w") > self::FRIDAY || $obDateTime->format("w") == self::SUNDAY) {
            if(in_array($todayFormatted, $arWorkDays))
                return true;
            return false;
        }

        return !in_array(($todayFormatted), $arHolidays);
    }

    /**
     * Ставит время в рамках рабочего дня (если оно выходит за рамки начала или конца рабочего дня)
     * @param \DateTime $dateTime
     * @return \DateTime
     */
    public static function setTimeToWorkingPeriod(\DateTime $obDateTime): \DateTime
    {
        $obWorkdayStartHour = $obDateTime;
        $obWorkdayStartHour->setTime(self::WORKDAY_START_HOUR,0,0);

        $obWorkdayEndHour = $obDateTime;
        $obWorkdayEndHour->setTime(self::WORKDAY_END_HOUR,0,0);


        if ($obDateTime < $obWorkdayStartHour) {
            // Время < Времени начала рабочего дня => Время = Времени начала рабочего дня
            $obDateTime->setTime(self::WORKDAY_START_HOUR,0,0);
        } elseif ($obDateTime > $obWorkdayEndHour) {
            // Время > Времени окончания рабочего дня => Время = Времени окончания рабочего дня
            $obDateTime->setTime(self::WORKDAY_END_HOUR,0,0);
        }

        // Дата == Выходной
        // => Время = Время окончания рабочего дня
        if (!self::isWorkDay($obDateTime)) {
            $obDateTime->setTime(self::WORKDAY_END_HOUR,0,0);
        }

        return $obDateTime;
    }


    /**
     * Возвращает конечную дату с учетом нерабочих дней (нерабочие дни прибавляются к сроку)
     *
     * @param string $startDate // Начальная дата
     * @param string $endDate   // Исходная конечная дата
     * @return string
     */
    public static function strExcludeWeekends(string $startDate, string $endDate)
    {
        $obStartDate = \DateTime::createFromFormat('d.m.Y H:i:s', $startDate);
        $obEndDate = \DateTime::createFromFormat('d.m.Y H:i:s', $endDate);
        $obStartDate->setTime($obEndDate->format("H"), $obEndDate->format("i"), $obEndDate->format("s"));

        $interval = $obStartDate->diff($obEndDate);
        $daysDiff = $interval->days;

        $obEndDate = self::objAddWorkDays($obStartDate, $daysDiff);

        return $obEndDate->format("d.m.Y H:i:s");
    }

    /**
     * Возвращает конечную дату с учетом нерабочих дней (нерабочие дни прибавляются к сроку)
     *
     * @param \DateTime $obStartDate // Начальная дата
     * @param \DateTime $obEndDate   // Исходная конечная дата
     * @return \DateTime|string
     */
    public static function objExcludeWeekends(\DateTime $obStartDate, \DateTime $obEndDate)
    {
        $obStartDate->setTime($obEndDate->format("H"), $obEndDate->format("i"), $obEndDate->format("s"));

        $interval = $obStartDate->diff($obEndDate);
        $daysDiff = $interval->days;

        $obEndDate = self::objAddWorkDays($obStartDate, $daysDiff);

        return $obEndDate;
    }
}
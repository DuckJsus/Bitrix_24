<?php

namespace NIC\Helpers\Sbis;

use NIC\Helpers\Logger;
use NIC\Helpers\Notifier;

class SbisException extends \Exception
{
    public function addLog()
    {
        $eventName = 'SBIS_ERROR';
        $name = 'SBIS_ERROR';
        $message = self::getMessage() . 'Код ошибки: ' . self::getCode();

        Logger::addLog($message, $eventName, $name);
    }

    public function addNotify()
    {
        $arUsers = \CGroup::GetGroupUser(1);
        $tag = 'SBIS_ERROR';
        $message = self::getMessage() . 'Код ошибки: ' . self::getCode();

        Notifier::messMe24($arUsers, $message, $tag);
    }
}
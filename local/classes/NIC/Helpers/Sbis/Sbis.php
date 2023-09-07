<?php
namespace NIC\Helpers\Sbis;

use Bitrix\Im\Log;
use NIC\Helpers\Logger;
use NIC\Helpers\Notifier;
use NIC\Helpers\Time;
use NIC\Helpers\UserRotation;

/**
 * Получение тендеров с платформы Сбис по Api
 * Создание элементов смарт-процессов (Лид ОГЗ)
 *
 * @class Sbis
 * @package NIC
 * @subpackage helpers
 * @author duckjsus
 * @email duckjsus@gmail.ru
 * @copyright ООО "НИЦ-ТЕХНОЛОГИИ" (c) 2023
 */
class Sbis
{
    private $login;
    private $password;

    private $retrain = 0;
    private $maxRetrain = 10;

    private $limit;
    private $performerFromOgz;

    // Адреса могут измениться
    private static $authUrl = 'https://online.sbis.ru/auth/service/';
    private static $serviceUrl = 'https://online.sbis.ru/tender-api/service/';
    private $authSid;

    // Папки из которых происходит получение тендеров
    private $arTenderFolders = [
        'Закупки 1',
        'Закупки 2',
        'Закупки 3',
    ];

    /**
     * Sbis constructor.
     *
     * @param string $login Логин в системе СБИС
     * @param string $password Пароль в системе СБИС
     */
    public function __construct()
    {
        $this->login = '';
        $this->password = '';
    }

    /**
     * Авторизация в СБИС
     *
     *
     * @see https://online.sbis.ru/shared/disk/26ee112b-db76-4bdc-9158-80068e60c108
     * @return string ID аутентифицированной сессии
     */
    public function auth()
    {
        if ($this->authSid) {
            return $this->authSid;
        }
        /**
         * "jsonrpc": "2.0", "method": "САП.Аутентифицировать", "protocol": 4, "params": {"login": "логин_учетной_записи","password": "пароль"}, "id": 1
         *
         */
        $result = $this->send(self::$authUrl, 'САП.Аутентифицировать', [
            'login' => $this->login,
            'password' => $this->password,
        ]);
        $this->authSid = $result['result'];
        return $this->authSid;
    }

    /**
     * Отправка запроса в СБИС
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws SbisException
     */
    private function send($url, $method, $params = [])
    {
        while ($this->retrain <= $this->maxRetrain+1) {
            $this->retrain++;

            $data = [
                'jsonrpc' => '2.0',
                'method' => $method,
                'protocol' => 4,
                'id' => 1,
            ];
            $curl = new Curl();
            if ($this->authSid) {
                $curl->setHeader('Cookie', 'sid=' . $this->authSid);
            }
            if (!empty($params)) {
                $data['params'] = $params;
            }
            $jsonData = json_encode($data);
            $curl->setHeader('Content-Type', 'application/json; charset=UTF-8');
            $curl->setHeader('User-Agent', 'PHP');

            $curl->setRawPostData($jsonData);
            $result = $curl->post($url, true);

            if (!$result) {
                $obExeption = new SbisException('Не получен ответ от СБИС по адресу ' . $url, 500);
                $obExeption->addLog();

                if ($this->retrain >= $this->maxRetrain) {
                    $obExeption = new SbisException('Превышено количество попыток. Не получен ответ от СБИС по адресу ' . $url, 500);
                    $obExeption->addLog();
                    $obExeption->addNotify();
                }

                continue;
            }

            $result = @json_decode($result, true);

            if (!$result) {
                $obExeption = new SbisException('Получен не JSON-ответ от СБИС по адресу ' . $url, 500);
                $obExeption->addLog();

                if ($this->retrain >= $this->maxRetrain) {
                    $obExeption = new SbisException('Превышено количество попыток. Получен не JSON-ответ от СБИС по адресу ' . $url, 500);
                    $obExeption->addLog();
                    $obExeption->addNotify();
                }

                continue;
            }

            if (isset($result['error']) && $result['error']) {
                //Возникла ошибка СБИС
                $obExeption = new SbisException('СБИС ответил ошибкой по адресу ' . $url . ': ' . $result['error']['details'] . ' ' . print_r($result,
                        true), $result['error']['code']);
                $obExeption->addLog();

                if ($this->retrain >= $this->maxRetrain) {
                    $obExeption = new SbisException('Превышено количество попыток. СБИС ответил ошибкой по адресу ' . $url . ': ' . $result['error']['details'] . ' ' . print_r($result,
                            true), $result['error']['code']);
                    $obExeption->addLog();
                    $obExeption->addNotify();
                }

                continue;
            }

            return $result;
        }
    }

    /**
     * Создает Лиды ОГЗ из тендеров
     *
     * @return void
     * @throws SbisException
     */
    public static function addTendersFromSbis()
    {
        $nextDay  = date("d.m.Y 08:00:00", strtotime("+1 day"));
        $agentID = \CAgent::AddAgent(
            'NIC\\Helpers\\Sbis\\Sbis::addTendersFromSbis("'.$nextDay.'");',
            "",
            "N",
            '0',
            $nextDay,
            "Y",
            $nextDay,
            5,
            949
        );
        if ($agentID) {
            Logger::addLog('Создан новый агент NIC\\Helpers\\Sbis\\Sbis::addTendersFromSbis("'.$nextDay.'");. ID='.$agentID, 'SBIS_AGENT', 'SBIS_AGENT');
        } else {
            $message = 'Ошибка при создании агента NIC\\Helpers\\Sbis\\Sbis::addTendersFromSbis("'.$nextDay.'");';

            Logger::addLog($message, 'SBIS_AGENT_ERROR', 'SBIS_AGENT_ERROR');
            foreach (\CGroup::GetGroupUser(1) as $userID) {
                Notifier::messMe24($userID, $message, 'SBIS_AGENT_ERROR');
            }
        }

        $obSbis = new Sbis();

        $obDate = \DateTime::createFromFormat('d.m.Y H:i:s', date('d.m.Y H:i:s'));
        $isWorkDay = Time::isWorkDay($obDate);

        $obUserRotation = new UserRotation('OGZ_LEAD');
        if($isWorkDay) {
            $obSbis->performerFromOgz = $obUserRotation->getThisDayUser();
        } else {
            $obSbis->performerFromOgz = $obUserRotation->getThisDayUser(false);
        }

        foreach ($obSbis->arTenderFolders as $folderName) {
            $result = $obSbis->getTenderListFromFolder($folderName);
            $obSbis->addTenders($result['result']['tenders'], $folderName);
        }
    }

    /**
     * Получает тендеры из папки в Сбис
     *
     * @param string $folderName
     * @return mixed
     * @throws SbisException
     */
    private function getTenderListFromFolder(string $folderName)
    {
        $this->auth();
        $this->getRemainingLimit();

        $data = [
            'params' => [
                'name' => $folderName,
                'limit' => $this->limit,
                'fromFolderAddDateTime' => date('Y-m-d 08:00:00', strtotime(date('Y-m-d 08:00:00') . '-1 day')),
                'toFolderAddDateTime' => date('Y-m-d 08:00:00'),
                'sortOrder' => 'desc',
            ]
        ];
        $result = $this->send(self::$serviceUrl, 'SbisTenderAPI.GetTenderListFromFolder', $data);

        return $result;
    }

    /**
     * Возвращает оставщийся лимит тендеров
     *
     * @return void
     * @throws SbisException
     */
    private function getRemainingLimit()
    {
        $data = [];
        $result = $this->send(self::$serviceUrl, 'SbisTenderAPI.GetStatistics', $data);
        $this->limit = $result['result']['DayRemaining'];
    }

    /**
     * Создает Лиды ОГЗ по тендерам из одной определенной папки
     *
     * @param array $arReceivingTenders
     * @param string $folderName
     * @return void
     */
    private function addTenders(array $arReceivingTenders, string $folderName)
    {

        foreach ($arReceivingTenders as $arReceivingTender) {
            $obSbisTender = new SbisTender($arReceivingTender, $folderName, $this->performerFromOgz);
            $obSbisTender->addTender();
        }
    }
}
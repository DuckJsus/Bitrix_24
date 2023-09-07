<?php

namespace NIC\Task;

use NIC\Helpers\Smart;

class Checklist
{
    /**
     * Получить Тэги из выполненных пунктов чеклиста
     * Метод для php кода в бизнес процессах
     *
     * @param int $taskID
     * @return array|void
     * @throws \CTaskAssertException
     */
    public static function getTagsCompletedItems(int $taskID)
    {
        $oTask = \CTaskItem::getInstance($taskID, Smart::BOT_FOR_TASKS);
        $arChecklist = $oTask->getData()['CHECKLIST'];
        if (!empty($arChecklist)) {
            foreach ($arChecklist as $item) {
                if ($item['IS_COMPLETE'] == 'Y') {
                    $titleFilter = '#TAG#=';
                    preg_match('/'.$titleFilter.'/', $item["TITLE"], $matches);

                    if (in_array($titleFilter, $matches)) {
                        $preg_repl = preg_replace('/.* '.$titleFilter.'/', '', $item["TITLE"]);

                        if ($preg_repl) {
                            $arReturn[] = $preg_repl;
                        }
                    }
                }
            }

            return $arReturn;
        }
    }
}
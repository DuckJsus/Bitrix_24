<?
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;
// пространство имен для получения ID модуля
use Bitrix\Main\HttpApplication;
// пространство имен для загрузки необходимых файлов, классов, модулей
use Bitrix\Main\Loader;
// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

CUtil::InitJSCore( array('ajax' , 'popup' ));
\Bitrix\Main\UI\Extension::load("ui.forms");


// получаем id модуля
$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

$APPLICATION->ShowHead();
Asset::getInstance()->addCss('/local/modules/' . $module_id . '/style.css', true);

?><script type="module" src='/local/modules/accessmodule.d7/lib/script/ajax.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/onclick.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/render.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/render_fields.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/render_roles.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/render_areas.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/main.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/templates/roles_page.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/templates/fields_page.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/templates/areas_page.js'></script><?
?><script type="module" src='/local/modules/accessmodule.d7/lib/script/popup.js'></script><?

// подключение модуля
Loader::includeModule($module_id);

// настройки модуля для админки в том числе значения по умолчанию
$aTabs = [];

// проверяем текущий POST запрос и сохраняем выбранные пользователем настройки
if ($request->isPost() && check_bitrix_sessid()) {
    // цикл по заполненым пользователем вкладкам
    foreach ($aTabs as $aTab) {
        foreach ($aTab["OPTIONS"] as $arOption) {
            // если это название секции, переходим к следующий итерации цикла
            if (!is_array($arOption)) {
                continue;
            }
            // Проверяем POST запрос, если инициатором выступила кнопка с name="apply" сохраняем введенные настройки в базу данных
            if ($request["apply"]) {
                // получаем в переменную $optionValue введенные пользователем данные
                $optionValue = $request->getPost($arOption[0]);
                // метод getPost() не работает с input типа checkbox, для работы сделал этот костыль
                if ($arOption[0] == "hmarketing_checkbox") {
                    if ($optionValue == "") {
                        $optionValue = "N";
                    }
                }
                // устанавливаем выбранные значения параметров и сохраняем в базу данных, перед сохранением проверяем если массив то соединяем данные, если не массив сохраняем как есть
                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            }
            // Проверяем POST запрос, если инициатором выступила кнопка с name="default" сохраняем дефолтные настройки в базу данных
            if ($request["default"]) {
                // устанавливаем дефолтные значения параметров и сохраняем в базу данных
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }
    // редирект на прежнию страницу
    LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . $module_id . "&lang=" . LANG);
}

// отрисовываем форму, для этого создаем новый экземпляр класса CAdminTabControl, куда и передаём массив с настройками
$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);

// отображаем заголовки закладок
$tabControl->Begin();
?>

    <form action="<? echo ($APPLICATION->GetCurPage()); ?>?mid=<? echo ($module_id); ?>&lang=<? echo (LANG); ?>" method="post">
        <? foreach ($aTabs as $aTab) {
            if ($aTab["OPTIONS"]) {
                // завершает предыдущую закладку, если она есть, начинает следующую
                $tabControl->BeginNextTab();
                // отрисовываем форму из массива
                __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
            }
        }
        // выводит стандартные кнопки отправки формы
        $tabControl->Buttons();
        // выводим скрытый input с идентификатором сессии
        echo (bitrix_sessid_post()); ?>
        <div id="access_module_container"></div>
        <input class="adm-btn-save" type="submit" name="apply" value="Применить" />
        <input type="submit" name="default" value="По умолчанию" />
    </form>
<?
// обозначаем конец отрисовки формы
$tabControl->End();

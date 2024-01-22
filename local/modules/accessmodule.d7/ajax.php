<?php
use Accessmodule\Main\Ajax;
use Accessmodule\Main\Main;
use Bitrix\Main\Loader;

Loader::includeModule("accessmodule.d7");

global $USER;
$userId = $USER->getId();

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
$action = $request->get('action');
$data = Main::array_change_key_case_upper($request->get('data'));

if ($USER->isAdmin()) {
    switch ($action) {
        case 'get_roles':
            $request = Ajax::getRoles();
            $request = Main::includeAdditionalInfo($request);
            break;
        case 'save_roles':
            $request = Ajax::saveRoles($data);
            $request = Main::includeAdditionalInfo($request);
            break;

        case 'get_fields':
            $request = Ajax::getFields();
            break;
        case 'save_fields':
            $request = Ajax::saveFields($data);
            break;

        case 'get_areas':
            $request = Ajax::getAreas();
            break;
        case 'save_areas':
            $request = Ajax::saveAreas($data);
            break;

        case 'reload_data':
            $request = Ajax::reloadData();
            $request = Main::includeAdditionalInfo($request);
            break;

        case 'get_additional_info':
            $request = Ajax::getAdditionalInfo($data);
            break;
    }
}

echo json_encode(Main::array_change_key_case_lower($request));
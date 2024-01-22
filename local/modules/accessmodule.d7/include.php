<?php
Bitrix\Main\Loader::registerAutoloadClasses(
    "accessmodule.d7",
    array(
        // ключ - имя класса с простанством имен, значение - путь относительно корня сайта к файлу
        "Accessmodule\\Main\\Main"                                  => "lib/Main.php",
        "Accessmodule\\Main\\EntityType"                            => "lib/EntityType.php",
        "Accessmodule\\Main\\Field"                                 => "lib/Field.php",
        "Accessmodule\\Main\\Ajax"                                  => "lib/Ajax.php",
        "Accessmodule\\Main\\AccessType"                            => "lib/AccessType.php",
        "Accessmodule\\Main\\Area"                                  => "lib/Area.php",
        "Accessmodule\\Main\\FieldControl"                          => "lib/FieldControl.php",
        "Accessmodule\\Service\\Container"                          => "lib/Service/Container.php",
        "Accessmodule\\Service\\EditorAdapter"                      => "lib/Service/EditorAdapter.php",
        "Accessmodule\\Service\\Factory"                            => "lib/Service/Factory.php",
        "Accessmodule\\Service\\ServiceManager"                     => "lib/Service/ServiceManager.php",
        "Accessmodule\\Service\\EntityEditor\\FieldManager"         => "lib/Service/EntityEditor/FieldManager.php",
        "Accessmodule\\Internals\\EditorConfig\\Config"             => "lib/Internals/EditorConfig/Config.php",
        "Accessmodule\\Data\\Access\\Role"                          => "lib/Data/Access/Role.php",
        "Accessmodule\\Data\\Access\\RoleUser"                      => "lib/Data/Access/RoleUser.php",
        "Accessmodule\\Data\\Access\\RoleGroup"                     => "lib/Data/Access/RoleGroup.php",
        "Accessmodule\\Data\\Access\\Access"                        => "lib/Data/Access/Access.php",
        "Accessmodule\\Data\\DB"                                    => "lib/Data/DB.php",
        "Accessmodule\\Data\\DataManager\\FieldDataTable"           => "lib/Data/DataManager/FieldDataTable.php",
        "Accessmodule\\Data\\DataManager\\EntityTypeDataTable"      => "lib/Data/DataManager/EntityTypeDataTable.php",
        "Accessmodule\\Data\\DataManager\\AccessTypeDataTable"      => "lib/Data/DataManager/AccessTypeDataTable.php",
        "Accessmodule\\Data\\DataManager\\RoleDataTable"            => "lib/Data/DataManager/RoleDataTable.php",
        "Accessmodule\\Data\\DataManager\\RoleGroupDataTable"       => "lib/Data/DataManager/RoleGroupDataTable.php",
        "Accessmodule\\Data\\DataManager\\RoleUserDataTable"        => "lib/Data/DataManager/RoleUserDataTable.php",
        "Accessmodule\\Data\\DataManager\\AreaDataTable"            => "lib/Data/DataManager/AreaDataTable.php",
    )
);

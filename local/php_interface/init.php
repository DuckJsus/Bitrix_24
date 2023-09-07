<?php
spl_autoload_register(function($class) {
    $file = $_SERVER["DOCUMENT_ROOT"] . '/local/classes/' . str_replace('\\','/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
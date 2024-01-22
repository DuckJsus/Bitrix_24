import * as ajax from "./ajax.js";
import * as main from "./main.js";

// Запрос на получение массива для отрисовки ролей
export function getRoles() {
    var data = {
        action: 'get_roles',
    };
    ajax.makeRequest(data, function(result, data) {
        var data = JSON.parse(data);
        localStorage.setItem('roles', JSON.stringify(data['roles']));
        main.renderRolePage(data);
    });
}

// Запрос на сохранение ролей
export function saveRoles(data) {
    ajax.makeRequest(data, function(result, data) {
        var data = JSON.parse(data);
        localStorage.setItem('roles', JSON.stringify(data['roles']));
        main.renderRolePage(data);
        alert('Сохранено');
    });
}

// Запрос на обновление полей и типов сущностей в БД
export function reloadData() {
    var data = {
        action: 'reload_data',
    };
    ajax.makeRequest(data, function(result, data) {
        var data = JSON.parse(data);
        localStorage.setItem('roles', JSON.stringify(data['roles']));
        main.renderRolePage(data);
        alert('Обновлено');
    });
}

// Запрос на получение массива для отрисовки полей
export function getFields() {
    var data = {
        action: 'get_fields',
    };
    ajax.makeRequest(data, function(result, data) {
        var data = JSON.parse(data);
        main.renderFieldsPage(data);
    });
}

// Запрос на сохранение полей
export function saveFields(data) {
    ajax.makeRequest(data, function(result, data) {
        var data = JSON.parse(data);
        main.renderFieldsPage(data);
        alert('Сохранено');
    });
}

// Запрос на получение массива для отрисовки областей
export function getAreas() {
    var data = {
        action: 'get_areas',
    };
    ajax.makeRequest(data, function(result, data) {
        var data = JSON.parse(data);
        localStorage.access_types = JSON.stringify(data['access_types']);
        main.renderAreasPage(data);
    });
}

// Запрос на сохранение областей
export function saveAreas(data) {
    ajax.makeRequest(data, function(result, data) {
        var data = JSON.parse(data);
        main.renderAreasPage(data);
        alert('Сохранено');
    });
}
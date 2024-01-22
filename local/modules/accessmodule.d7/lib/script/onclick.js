import * as request from "./request.js";

// ajax запрос на сохранение изменений в ролях
export function AccessmoduleSaveRoles() {
    var groups = [];
    $('.accessmodule_group').each(function(i, obj) {
        var group;
        group = {
            id: $(obj).attr('data-id'),
            role_id: $(obj).find('select option:selected').attr('data-id'),
            group_id: $(obj).attr('data-group_id'),
        };
        groups.push(group);
    });

    var users = [];
    $('.accessmodule_user').each(function(i, obj) {
        var user;
        user = {
            id: $(obj).attr('data-id'),
            role_id: $(obj).find('select option:selected').attr('data-id'),
            user_id: $(obj).attr('data-user_id'),
        };
        users.push(user);
    });

    var roles = [];
    $('.accessmodule_role').each(function (i, obj) {
        var role;
        role = {
            id: $(obj).attr('data-id'),
            name: $(obj).attr('data-name'),
            lang_name: $(obj).attr('data-lang_name'),
        };
        roles.push(role);
    });


    var data = {
        action: 'save_roles',
        data: {
            roles: roles,
            groups: groups,
            users: users,
        },
    };

    request.saveRoles(data);
}

// Удаление привязки роли / области со страницы
export function AccessmoduleDeleteObj(obj) {
    $(obj).remove();
}

// Удаление роли со страницы
export function AcccessmoduleDeleteRole(objRole) {
    var roleId = $(objRole).attr('data-id');

    $('.accessmodule_group').each(function (i, objGroup) {
        var objOption = $(objGroup).find('select option[data-id="'+roleId+'"]');
        if ($(objOption).is(':selected')) {
            $(objGroup).remove();
        } else {
            $(objOption).remove();
        }
    });

    $('.accessmodule_user').each(function (i, objUser) {
        var objOption = $(objUser).find('select option[data-id="'+roleId+'"]');
        if ($(objOption).is(':selected')) {
            $(objUser).remove();
        } else {
            $(objOption).remove();
        }
    });

    $(objRole).remove();
}

// Переход на страницу редактирования полей
export function AccessmoduleEditFields() {
    request.getFields();
}

// Обновление полей и типов сущностей
export function AccessmoduleReloadData() {
    request.reloadData();
}

// Сохранение изменений полей в БД
export function AccessmoduleSaveFields() {
    var editableRole = JSON.parse(localStorage.editableRole);
    var roles = JSON.parse(localStorage.roles);
    delete roles[editableRole['id']];

    var fields = [];

    $('.accessmodule_field').each(function(i, obj) {
        var field;
        field = {
            id: $(obj).attr('data-id'),
            name: $(obj).attr('data-name'),
            lang_name: $(obj).attr('data-lang_name'),
            entity_type_id: $(obj).attr('data-entity_type_id'),
            standard_type: $(obj).attr('data-standard_type'),
        };
        field[editableRole['name'].toLowerCase()] = $(obj).find('select option:selected').val();
        $.each(roles, function (key, role) {
           field[role['name'].toLowerCase()] =  $(obj).attr('data-'+role['name'].toLowerCase());
        });

        fields.push(field);
    });

    var data = {
        action: 'save_fields',
        data: {
            fields: fields,
        },
    };

    request.saveFields(data);
}

// Переход на страницу редактирования областей
export function AccessmoduleEditAreas() {
    request.getAreas();
}

// Сохранение изменений областей в БД
export function AccessmoduleSaveAreas() {
    var editableRole = JSON.parse(localStorage.editableRole);
    var roles = JSON.parse(localStorage.roles);
    delete roles[editableRole['id']];

    var areas = [];

    $('.accessmodule_area').each(function(i, obj) {
        var area;
        area = {
            id: $(obj).attr('data-id'),
            name: $(obj).attr('data-name'),
            lang_name: $(obj).attr('data-lang_name'),
        };
        area[editableRole['name'].toLowerCase()] = $(obj).find('select option:selected').val();
        $.each(roles, function (key, role) {
            area[role['name'].toLowerCase()] =  $(obj).attr('data-'+role['name'].toLowerCase());
        });

        areas.push(area);
    });

    var data = {
        action: 'save_areas',
        data: {
            areas: areas,
        },
    };

    request.saveAreas(data);
}
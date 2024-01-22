import * as onclick from "./onclick.js";
import * as roles_page from "./templates/roles_page.js";

// Отрисовка основного контейнера Ролей
export function renderRolesContainer() {
    $(".adm-detail-content-wrap").append(roles_page.accessmodule_roles_container);

    $('#accessmodule_save_roles').on('click', function() {
        onclick.AccessmoduleSaveRoles();
    });
    $('#accessmodule_reload_data').on('click', function() {
        onclick.AccessmoduleReloadData();
    });
}

// Отрисовка ролей
export function render_roles(data) {
    var roles = data['roles'];

    $.each(roles, function (key, role) {
        renderRole(role);
    });
}

// Отрисовка конкретной роли
export function renderRole(role) {
    var accessmodule_role_html = '<div class="accessmodule_role" data-id="'+ role["id"] +'" data-name="'+ role["name"] +'" data-lang_name="'+ role["lang_name"] +'">' +
        '                               <a href="#" style="float:right" title="Удалить" class="accessmodule_delete accessmodule_delete_role"></a>\n';
    if (role['id']) {
        accessmodule_role_html = accessmodule_role_html + '<a href="#" style="float:right" title="Редактировать" class="accessmodule_edit accessmodule_edit_fields"></a>\n';
    }

    accessmodule_role_html = accessmodule_role_html + '<div style="padding-bottom: 4px" algin="left">- '+ role["lang_name"] +'</div>\n' +
        '                               <div style="clear:both"></div>' +
        '                           </div>';
    $("#accessmodule_button_add_role").before(accessmodule_role_html);

    $('.accessmodule_delete_role').on('click', function() {
        onclick.AcccessmoduleDeleteRole(this.parentNode);
    });
    $('.accessmodule_edit_fields').on('click', function() {
        var objRole = this.parentNode;
        var id = $(objRole).attr('data-id');
        var name = $(objRole).attr('data-name');
        var lang_name = $(objRole).attr('data-lang_name');
        var role = {
            id: id,
            name: name,
            lang_name: lang_name,
        };
        localStorage.editableRole = JSON.stringify(role);

        onclick.AccessmoduleEditFields();
    });
}

// Отрисовка привязок групп
export function renderGroups(data) {
    var roles = data['roles'];
    var groups = data['groups'];

    $.each(groups, function(key, group) {
        var roles_html = renderRolesSelect(roles, group['role_id']);

        renderGroup(group, roles_html);
    });
}

// Отрисовка приваязок пользователей
export function renderUsers(data) {
    var roles = data['roles'];
    var users = data['users'];

    $.each(users, function(key, user) {
        var roles_html = renderRolesSelect(roles, user['role_id']);

        renderUser(user, roles_html);
    });
}

// Отрисовка выбора ролей для привязки
export function renderRolesSelect(roles, id = 0) {
    var roles_html = '';
    $.each(roles, function (key, role) {
        if (role['id'] == id) {
            roles_html = roles_html + '<option class="accessmodule_role_option" selected data-id="'+ role['id'] +'" data-name="'+ role['name'] +'" data-lang_name="'+ role['lang_name'] +'">'+ role['lang_name'] +'</option>\n';
        } else {
            roles_html = roles_html + '<option class="accessmodule_role_option" data-id="'+ role['id'] +'" data-name="'+ role['name'] +'" data-lang_name="'+ role['lang_name'] +'">'+ role['lang_name'] +'</option>\n';
        }
    });

    return roles_html;
}

// Отрисовка привязки группы
function renderGroup(group, roles_html) {
    $("#accessmodule_button_add_role_bind").before('<tr class="accessmodule_group accessmodule_bind" data-id="' + group['id'] + '" data-group_id="' + group['group_id'] +
        '">\n' +
        '                    <td><b>Группа:</b> ' + group['name']+ ' [' + group['group_id'] + ']</td>\n' +
        '                    <td class="last-child">\n' +
        '                        <div style="float:left">\n' +
        '                            <select>\n' +
        roles_html +
        '                            </select>\n' +
        '                        </div>\n' +
        '                        <a href="#"  class="accessmodule_delete accessmodule_delete_role_bind" title="Удалить"></a>\n' +
        '                    </td>\n' +
        '                </tr>'
    );

    $('.accessmodule_delete_role_bind').on('click', function() {
        onclick.AccessmoduleDeleteObj(this.parentNode.parentNode);
    });
}

// Отрисовка привязки пользователя
function renderUser(user, roles_html) {
    $("#accessmodule_button_add_role_bind").before('<tr class="accessmodule_user accessmodule_bind" data-id="' + user['id'] + '" data-user_id="' + user['user_id'] +
        '">\n' +
        '                    <td><b>Пользователь:</b> ' + user['name'] + ' [' + user['user_id'] + ']</td>\n' +
        '                    <td class="last-child">\n' +
        '                        <div style="float:left">\n' +
        '                            <select>\n' +
        roles_html +
        '                            </select>\n' +
        '                        </div>\n' +
        '                        <a href="#" class="accessmodule_delete accessmodule_delete_role_bind" title="Удалить"></a>\n' +
        '                    </td>\n' +
        '                </tr>'
    );

    $('.accessmodule_delete_role_bind').on('click', function() {
        onclick.AccessmoduleDeleteObj(this.parentNode.parentNode);
    });
}
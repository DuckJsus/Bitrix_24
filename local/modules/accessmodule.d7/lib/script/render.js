// Очистка основного контейнера модуля
export function cleanContainer() {
    $('.adm-detail-content-wrap').empty();
}

// Отрисовка списка выбора типа доступа
export function renderAccessSelect(access_types) {
    var access_select_html = '';
    $.each(access_types, function (key, access) {
        access_select_html = access_select_html + '<option class="accessmodule_access_option" value="'+access['id']+'">'+ access['name'] +'</option>\n';
    });

    return access_select_html;
}

// Добавление data-attr на элементы ролей
export function renderRolesDataTags(obj) {
    var role_data_tags = '';
    var roles = JSON.parse(localStorage.roles);
    $.each(roles, function (key, role) {
        var role_name = role['name'].toLowerCase();
        role_data_tags = role_data_tags + ' data-' + role_name + '="' + obj[role_name] + '"';
    });
    return role_data_tags;
}
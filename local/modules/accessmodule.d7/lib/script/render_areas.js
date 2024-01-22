import * as areas_page from "./templates/areas_page.js";
import * as onclick from "./onclick.js";
import * as render from "./render.js";

// Отрисовка основного контейнера Областей
export function renderAreasContainer() {
    $(".adm-detail-content-wrap").append(areas_page.accessmodule_fields_container);
    $("#accessmodule_button_save_areas").on('click', function() {
        onclick.AccessmoduleSaveAreas();
    });
    $("#accessmodule_edit_fields").on('click', function() {
        onclick.AccessmoduleEditFields();
    });
}

// Отрисовка названия редактируемой роли на странице Областей
export function renderRoleData() {
    var role = JSON.parse(localStorage.editableRole);
    $('.accessmodule_areas').prepend('<div data-id="'+role['id']+'" data-name="'+role['name']+'" data-lang_name="'+role['lang_name']+'">Роль: '+role['lang_name']+'</div>');
}

// Отрисовка основных данных
export function renderPageData(data) {
    renderAreas(data);
}

// Отрисовка Областей на странице
export function renderAreas(data) {
    var access_select_html = render.renderAccessSelect(JSON.parse(localStorage.access_types));
    var areas = data['areas'];

    $.each(areas, function (key, area) {
        renderArea(area, access_select_html);
    });
    $('.accessmodule_delete_area').on('click', function() {
        onclick.AccessmoduleDeleteObj(this.parentNode.parentNode);
    });
}

// Отрисовка конкретной роли
export function renderArea(area, access_select_html) {
    var role_data_tags = '';
    if (area['id']) {
        role_data_tags = render.renderRolesDataTags(area);
    }
    var role = JSON.parse(localStorage.editableRole);
    var access_id = area[role['name'].toLowerCase()];

    $("#accessmodule_areas_table").append('<tr class="accessmodule_area" id="area_'+ area['id'] +'" value="'+area['id']+'"'+
        '   data-id="'+area['id']+'" data-name="'+area['name']+'" data-lang_name="'+area['lang_name']+'"'+
        role_data_tags + '>\n' +
        '                    <td>'+ area['lang_name'] +'</td>\n' +
        '                    <td class="last-child">\n' +
        '                        <div style="float:left">\n' +
        '                            <select>\n' +
        access_select_html +
        '                            </select>\n' +
        '                        </div>\n' +
        '                        <a href="#" style="float:right" title="Удалить" class="accessmodule_delete accessmodule_delete_area"></a>\n' +
        '                    </td>\n' +
        '                </tr>'
    );
    $('#area_' +area['id']).find('select').val(access_id);
}
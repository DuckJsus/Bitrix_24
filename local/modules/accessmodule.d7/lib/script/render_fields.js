import * as fields_page from "./templates/fields_page.js";
import * as onclick from "./onclick.js";
import * as render from "./render.js";

// Отрисовка основного контейнера полей
export function renderFieldsContainer() {
    $(".adm-detail-content-wrap").append(fields_page.accessmodule_fields_container);
    $("#accessmodule_button_save_fields").on('click', function() {
        onclick.AccessmoduleSaveFields();
    });
    $("#accessmodule_edit_areas").on('click', function() {
        onclick.AccessmoduleEditAreas();
    });
}

// Отрисовка названия редактируемой роли на странице полей
export function renderRoleData() {
    var role = JSON.parse(localStorage.editableRole);
    $('.accessmodule_fields').prepend('<div data-id="'+role['id']+'" data-name="'+role['name']+'" data-lang_name="'+role['lang_name']+'">Роль: '+role['lang_name']+'</div>');
}

// Отрисовка основных данных
export function renderPageData(data) {
    var access_select_html = render.renderAccessSelect(data['access_types']);
    renderEntityTypes(data['entity_types']);
    renderFields(data['fields'], access_select_html);
}

// Отрисовка Полей на странице
export function renderFields(fields, access_select_html) {
    $.each(fields, function (key, field) {
        renderField(field, access_select_html);
    });
}

// Отрисовка конкретного поля
function renderField(field, access_select_html) {
    var role_data_tags = render.renderRolesDataTags(field);
    var role = JSON.parse(localStorage.editableRole);
    var access_id = field[role['name'].toLowerCase()];

    $("#entity_type_"+field['entity_type_id']).after('<tr class="accessmodule_field" id="field_'+ field['id'] +'" value="'+field['id']+'"'+
        '   data-id="'+field['id']+'" data-name="'+field['name']+'" data-lang_name="'+field['lang_name']+'"'+
        '   data-entity_type_id="'+field['entity_type_id']+'" data-standard_type="'+field['standard_type']+'"'+
            role_data_tags + '>\n' +
        '                    <td>'+ field['lang_name'] +'</td>\n' +
        '                    <td class="last-child">\n' +
        '                        <div style="float:left">\n' +
        '                            <select>\n' +
                                        access_select_html +
        '                            </select>\n' +
        '                        </div>\n' +
        '                    </td>\n' +
        '                </tr>'
    );
    $('#field_' +field['id']).find('select').val(access_id);
}

// Отрисовка типов сущностей
function renderEntityTypes(entity_types) {
    $.each(entity_types, function (key, entity_type) {
        renderEntityType(entity_type);
    });
}

// Отрисовка типа сущности
function renderEntityType(entity_type) {
    $("#accessmodule_fields_table").append('<tr class="accessmodule_entity_type" id="entity_type_'+ entity_type['id'] +'" data-id="'+entity_type['id']+'"' +
        '   data-name="'+entity_type['name']+'" data-standard_type="'+entity_type['standard_type']+'">\n' +
        '                    <td><b>'+ entity_type['name'] +'</b></td>\n' +
        '                    <td class="last-child">\n' +

        '                    </td>\n' +
        '                </tr>'
    );
}
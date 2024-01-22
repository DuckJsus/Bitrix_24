import * as request from './request.js';
import * as render from './render.js';
import * as render_roles from './render_roles.js';
import * as render_fields from './render_fields.js';
import * as render_areas from './render_areas.js';
import * as popup from './popup.js';

render.cleanContainer();
request.getRoles();

// Отрисовка страницы ролей при обновлении/загрузке страницы
export function renderRolePage(data) {
    render.cleanContainer();
    render_roles.renderRolesContainer();
    render_roles.renderGroups(data);
    render_roles.renderUsers(data);
    render_roles.render_roles(data);
    $('#accessmodule_button_add_role_bind').on('click', function(){
        popup.bindPopupCreate();
    });
    $('#accessmodule_button_add_role').on('click', function(){
        popup.rolePopupCreate();
    });
}

// Отрисовка страницы полей
export function renderFieldsPage(data) {
    render.cleanContainer();
    render_fields.renderFieldsContainer();
    render_fields.renderRoleData();
    render_fields.renderPageData(data);
    $('.accessmodule_entity_type').click(function(){
        $(this).nextUntil('tr.accessmodule_entity_type').slideToggle(1000);
    });
}

//Отрисовка страницы областей
export function renderAreasPage(data) {
    render.cleanContainer();
    render_areas.renderAreasContainer();
    render_areas.renderRoleData();
    render_areas.renderPageData(data);
    $('.accessmodule_entity_type').click(function(){
        $(this).nextUntil('tr.accessmodule_entity_type').slideToggle(1000);
    });
    $('#accessmodule_button_add_area').on('click', function(){
        popup.areaPopupCreate();
    });
}
import * as ajax from "./ajax.js";
import * as roles_page from "./templates/roles_page.js";
import * as areas_page from "./templates/areas_page.js";
import * as render_roles from "./render_roles.js";
import * as render_areas from "./render_areas.js";

// Popup окно добавления привязки роли
export function bindPopupCreate () {
    BX.ready(function(){
        var addRoleBind = new BX.PopupWindow('accessmodule-bind-popup', null, {
            // content: BX('accessmodule-bind-popup'),
            closeIcon: {right: "20px", top: "10px"},
            titleBar: {content: BX.create("span", {html: '<b>Добавьте право доступа</b>', 'props': {'className': 'accessmodule-bind-popup-title-bar'}})},
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,

            autoHide : true,
            lightShadow : true,
            closeByEsc : true,
            overlay: {backgroundColor: 'black', opacity: '80' },  /* затемнение фона */

            draggable: {restrict: false},
            buttons: [
                new BX.PopupWindowButton({
                    text: "Добавить",
                    className: "popup-window-button-accept popup-window-button-accept-accessmodule-bind",
                    events: {click: function(){
                        if (checkFormstep('popup-window-content-accessmodule-bind-popup')) {
                            AccessmoduleRoleBindInsert();
                            this.popupWindow.destroy(); // закрытие окна
                        }
                    }}
                }),
                new BX.PopupWindowButton({
                    text: "Закрыть",
                    className: "webform-button-link-cancel",
                    events: {click: function(){
                            this.popupWindow.destroy(); // закрытие окна
                        }}
                })
            ]
        });
        $('#popup-window-content-accessmodule-bind-popup').append(roles_page.accessmodule_form_add_role_bind);
        addRoleBind.show(); // появление окна
    })
};

// Проверка обязательных полей
function checkFormstep(id) {
    var notFilled;
    $('#'+id).find('input').each(function(){
        if($(this).prop('required') && $(this).val() == false){
            notFilled = true;
            $(this).parent().prepend('<div class="ui-ctl-tag-danger">Обязательное поле</div>');
            $(this).parent().addClass('ui-ctl-danger');
        }
    });

    if (notFilled) {
        return false;
    } else {
        return true;
    }
}

// Popup окно добавления новой роли
export function rolePopupCreate () {
    BX.ready(function(){
        var addRole = new BX.PopupWindow('accessmodule-role-popup', null, {
            closeIcon: {right: "20px", top: "10px"},
            titleBar: {content: BX.create("span", {html: '<b>Добавьте право доступа</b>', 'props': {'className': 'accessmodule-role-popup-title-bar'}})},
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,

            autoHide : true,
            lightShadow : true,
            closeByEsc : true,
            overlay: {backgroundColor: 'black', opacity: '80' },  /* затемнение фона */

            draggable: {restrict: false},
            buttons: [
                new BX.PopupWindowButton({
                    text: "Добавить",
                    className: "popup-window-button-accept popup-window-button-accept-accessmodule-role",
                    events: {click: function(){
                            checkFormstep('popup-window-content-accessmodule-role-popup');
                            AccessmoduleRoleInsert();
                            this.popupWindow.destroy(); // закрытие окна
                        }}
                }),
                new BX.PopupWindowButton({
                    text: "Закрыть",
                    className: "webform-button-link-cancel",
                    events: {click: function(){
                            this.popupWindow.destroy(); // закрытие окна
                        }}
                })
            ]
        });
        $('#popup-window-content-accessmodule-role-popup').append(roles_page.accessmodule_form_add_role);
        addRole.show(); // появление окна
    })
};

// Отрисовка на страницк новой привязки роли
function AccessmoduleRoleBindInsert() {
    var type = $('#popup-window-content-accessmodule-bind-popup').find('select option:selected').val();
    var id = $('#popup-window-content-accessmodule-bind-popup').find('#accessmodule_popup_field_bind_id').val();

    var data = {
        action: 'get_additional_info',
        data: {
            type: type,
            id: id,
        },
    };
    ajax.makeRequest(data, function(result, data) {
        var data = JSON.parse(data);
        var name = data['data']['name'];
        data = {
            roles: JSON.parse(localStorage.roles),
        };
        if (type == 'group') {
            data['groups'] = [{
                id: 0,
                role_id: 0,
                group_id: id,
                name: name,
            }];
            $('#accessmodule_button_add_role_bind').before(render_roles.renderGroups(data));
        } else if (type == 'user') {
            data['users'] = [{
                id: 0,
                role_id: 0,
                user_id: id,
                name: name,
            }];
            $('#accessmodule_button_add_role_bind').before(render_roles.renderUsers(data));
        }
    });
}

// Отрисовка на странице новой роли
function AccessmoduleRoleInsert() {
    var name = $('#popup-window-content-accessmodule-role-popup').find('#accessmodule_popup_field_role_name').val();
    var langName = $('#popup-window-content-accessmodule-role-popup').find('#accessmodule_popup_field_role_lang_name').val();

    var role = {
        id: 0,
        name: name,
        lang_name: langName,
    };

    render_roles.renderRole(role);
}

// Popup окно добавления новой области
export function areaPopupCreate () {
    BX.ready(function(){
        var addArea = new BX.PopupWindow('accessmodule-area-popup', null, {
            closeIcon: {right: "20px", top: "10px"},
            titleBar: {content: BX.create("span", {html: '<b>Добавьте область</b>', 'props': {'className': 'accessmodule-area-popup-title-bar'}})},
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,

            autoHide : true,
            lightShadow : true,
            closeByEsc : true,
            overlay: {backgroundColor: 'black', opacity: '80' },  /* затемнение фона */

            draggable: {restrict: false},
            buttons: [
                new BX.PopupWindowButton({
                    text: "Добавить",
                    className: "popup-window-button-accept popup-window-button-accept-accessmodule-area",
                    events: {click: function(){
                            checkFormstep('popup-window-content-accessmodule-area-popup');
                            AccessmoduleAreaInsert();
                            this.popupWindow.destroy(); // закрытие окна
                        }}
                }),
                new BX.PopupWindowButton({
                    text: "Закрыть",
                    className: "webform-button-link-cancel",
                    events: {click: function(){
                            this.popupWindow.destroy(); // закрытие окна
                        }}
                })
            ]
        });
        $('#popup-window-content-accessmodule-area-popup').append(areas_page.accessmodule_form_add_area);
        addArea.show(); // появление окна
    })
};

// Отрисовка на странице новой области
function AccessmoduleAreaInsert() {
    var name = $('#popup-window-content-accessmodule-area-popup').find('#accessmodule_popup_field_area_name').val();
    var langName = $('#popup-window-content-accessmodule-area-popup').find('#accessmodule_popup_field_area_lang_name').val();

    var data = {
        areas: [{
            id: 0,
            name: name,
            lang_name: langName,
        }]
    };

    render_areas.renderAreas(data);
}
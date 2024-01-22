// Основной контейнер страницы ролей
export var accessmodule_roles_container = '<div class="workarea-content-paddings">\n' +
    '               <div id="accessmodule_reload_data">\n' +
    '                   <button type="submit">Обновить данные</button>\n' +
    '               </div>\n' +
    '    <table width="100%" cellpadding="0" cellspacing="0">\n' +
    '        <tbody><tr>\n' +
    '            <td valign="top" style="min-width:432px">\n' +
    '                <table class="accessmodule_roles" width="100%" cellpadding="0" cellspacing="0">\n' +
    '                    <tbody id="accessmodule_roles">\n' +
    '                    <tr>\n' +
    '                        <th>&nbsp;</th>\n' +
    '                        <th>Роль</th>\n' +
    '                    </tr>\n' +

    '                       <tr id="accessmodule_button_add_role_bind" class="accessmodule_add"><td colSpan="2"><a href="#">Добавить право доступа</a></td></tr>\n' +
    '                    </tbody></table>\n' +
    '            </td>\n' +
    '            <td style="padding-left:15px; min-width:192px;" valign="top">\n' +
    '                <table width="100%" cellpadding="0" cellspacing="0">\n' +
    '                    <tbody><tr>\n' +
    '                        <th>Список ролей:</th>\n' +
    '                    </tr>\n' +
    '                    <tr>\n' +
    '                        <td id="accessmodule_role_table">\n' +

    '                           <div id="accessmodule_button_add_role" class="accessmodule_add" style="padding-left:10px"><a href="#">Добавить</a></div>\n' +
    '                        </td>\n' +
    '                    </tr>\n' +
    '                    </tbody></table>\n' +
    '            </td>\n' +
    '        </tr>\n' +
    '        </tbody></table>\n' +
    '            <div id="accessmodule_save_roles">\n' +
    '                <button type="submit">Сохранить</button>\n' +
    '            </div>\n' +
    '</div>';

// Popup окно добавления новой роли
export var accessmodule_form_add_role = '<div class="ui-form">\n' +
    '    <div class="ui-form-row">\n' +
    '        <div class="ui-form-label">\n' +
    '            <div class="ui-ctl-label-text">Код роли</div>\n' +
    '        </div>\n' +
    '        <div class="ui-form-content ui-ctl">\n' +
    '                <input type="text" id="accessmodule_popup_field_role_name" class="ui-ctl-element ui-ctl-w75 accessmodule_role_name" placeholder="ROLE_TEST_NAME" required>\n' +
    '            </select>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '    <div class="ui-form-row">\n' +
    '        <div class="ui-form-label">\n' +
    '            <div class="ui-ctl-label-text">Название роли</div>\n' +
    '        </div>\n' +
    '        <div class="ui-form-content">\n' +
    '            <div class="ui-ctl ui-ctl-textbox">\n' +
    '                <input type="text" id="accessmodule_popup_field_role_lang_name" class="ui-ctl-element ui-ctl-w75 accessmodule_role_lang_name" placeholder="Тестовое название" required>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>';

// Popup окно добавления новой привязки к роли
export var accessmodule_form_add_role_bind = '<div class="ui-form">\n' +
    '    <div class="ui-form-row">\n' +
    '        <div class="ui-form-label">\n' +
    '            <div class="ui-ctl-label-text">Тип прав доступа</div>\n' +
    '        </div>\n' +
    '        <div class="ui-form-content ui-ctl">\n' +
    '            <select class="ui-ctl-element ui-ctl-w75 accessmodule_bind_type">\n' +
    '                <option value="group" selected="true">Группы</option>\n' +
    '                <option value="user">Пользователь</option>\n' +
    '            </select>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '    <div class="ui-form-row">\n' +
    '        <div class="ui-form-label">\n' +
    '            <div class="ui-ctl-label-text">ID Группы/Пользователя</div>\n' +
    '        </div>\n' +
    '        <div class="ui-form-content">\n' +
    '            <div class="ui-ctl ui-ctl-textbox">\n' +
    '                <input type="numdber" id="accessmodule_popup_field_bind_id" class="ui-ctl-element ui-ctl-w75 accessmodule_bind" placeholder="123" required>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>';
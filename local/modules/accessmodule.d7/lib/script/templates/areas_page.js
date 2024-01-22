// Основной контейнер страницы областей
export var accessmodule_fields_container = '<div class="workarea-content-paddings">\n' +

    '            <div id="accessmodule_edit_fields">\n' +
    '                <button type="submit">Редактировать поля</button>\n' +
    '            </div>\n' +

    '    <table width="100%" cellpadding="0" cellspacing="0">\n' +
    '        <tbody><tr>\n' +
    '            <td valign="top" style="min-width:432px">\n' +
    '                <table class="accessmodule_areas" width="100%" cellpadding="0" cellspacing="0">\n' +
    '                    <tbody id="accessmodule_areas_table">\n' +
    '                    <tr>\n' +
    '                       <th>Область</th>\n' +
    '                       <th>Чтение</th>\n' +
    '                    </tr>\n' +

    '                    </tbody></table>\n' +
    '            </td>\n' +
    '        </tr></tbody>\n' +
    '    </table>\n' +

    '            <div id="accessmodule_button_add_area" class="accessmodule_add" style="padding-left:10px"><a href="#">Добавить</a></div>\n' +
    '            <div id="accessmodule_button_save_areas">\n' +
    '                <button type="submit">Сохранить</button>\n' +
    '            </div>\n' +
    '</div>';

// Popup контейнер добавления новой области
export var accessmodule_form_add_area = '<div class="ui-form">\n' +
    '    <div class="ui-form-row">\n' +
    '        <div class="ui-form-label">\n' +
    '            <div class="ui-ctl-label-text">Код области</div>\n' +
    '        </div>\n' +
    '        <div class="ui-form-content ui-ctl">\n' +
    '                <input type="text" id="accessmodule_popup_field_area_name" class="ui-ctl-element ui-ctl-w75 accessmodule_area_name" placeholder="TEST_NAME" required>\n' +
    '            </select>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '    <div class="ui-form-row">\n' +
    '        <div class="ui-form-label">\n' +
    '            <div class="ui-ctl-label-text">Название области</div>\n' +
    '        </div>\n' +
    '        <div class="ui-form-content">\n' +
    '            <div class="ui-ctl ui-ctl-textbox">\n' +
    '                <input type="text" id="accessmodule_popup_field_area_lang_name" class="ui-ctl-element ui-ctl-w75 accessmodule_area_lang_name" placeholder="Тестовое название" required>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>';

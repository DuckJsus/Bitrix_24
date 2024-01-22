// Тело Ajax запроса
export function makeRequest(data, callback) {
    $.ajax({
        type: 'POST',
        url: '/accessmodule.d7/access_module.php',
        data: data,
        success: function (data) {
            callback(true, data);
        },
        error: function (jqXHR) {
            callback(false, jqXHR.status);
        }
    });
}
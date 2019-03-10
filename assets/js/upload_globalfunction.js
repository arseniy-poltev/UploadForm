function getFileExtension(filename) {
    return filename.split('.').pop();
}

function loader() {
    $('#loader_modal').modal('show');
}

function exit_loader() {
    $('#loader_modal').modal('hide');
}


function disp_alert(header, content) {
    $('#dialog_modal').find('.modal-title').html(header);
    $('#dialog_modal').find('.modal-body').html(content);
    $('#dialog_modal').modal('show');
}

function dispSuccessAlarm(content) {
    $('#success_alarm').css('display', 'block');
}

function hideSuccessAlart() {
    $('#success_alarm').css('display', 'none');
}

function hide_alert() {
    $('#dialog_modal').modal('hide');
}

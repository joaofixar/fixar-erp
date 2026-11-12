function importarTray(divId){
    $('#' + divId).showLoading();
    $.getJSON('/sistema/ajax/importarTray/?ajax=true&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code) { 
           dialogConfirm('Pedidos importados Tray', request.data, false);
        } else {
            dialogConfirm('Erro', request.data, false);
        }
    });
}
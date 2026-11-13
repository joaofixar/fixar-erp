function processarPedidosVencidos(divId){
    $('#' + divId).showLoading();
    $.getJSON('/sistema/ajax/processarPedidosVencidos/?ajax=true&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code) { 
           dialogConfirm('Processar Vencidos', request.data, false);
        } else {
            dialogConfirm('Erro', request.data, false);
        }
    });
}
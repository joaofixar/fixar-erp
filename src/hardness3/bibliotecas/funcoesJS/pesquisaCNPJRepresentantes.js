function pesquisaCNPJRepresentantes(divId,divIdRoot,CNPJ) {
    var CNPJ = $("#" + divId + " #" + CNPJ).val();
    console.log('CNPJ ' + CNPJ);
    $('#' + divId).showLoading();
    $.getJSON('/sistema/funcoes/pesquisaCNPJ/consultaApiRepresentantes/?ajax=true&CNPJ=' + encodeURIComponent(CNPJ) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code){
            $.each(request.data, function (key, value) {
                console.log(key + ' => ' + value);
                if(key=='D021_D020_Id'){
                     $("#" + divId + " #" + key).html(value);
                } else {
                    $("#" + divId + " #" + key).val(value);
                }
            });
            reloadUI('#' . divId);
        }else{
            dialogConfirm('Erro', request.erro);
        }
    });
} 




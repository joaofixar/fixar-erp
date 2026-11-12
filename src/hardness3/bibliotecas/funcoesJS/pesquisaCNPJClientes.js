function pesquisaCNPJClientes(divId,divIdRoot,CNPJ) {
    var CNPJ = $("#" + divId + " #" + CNPJ).val();
    $('#' + divId).showLoading();
    $.getJSON('/sistema/funcoes/pesquisaCNPJ/consultaApiClientes/?ajax=true&CNPJ=' + encodeURIComponent(CNPJ) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code){
            $.each(request.data, function (key, value) {
                if(key=='D024_D020_Id'){
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

function pesquisaIEClientes(divId,divIdRoot,CNPJ) {
    var CNPJ = $("#" + divId + " #" + CNPJ).val();
    $('#' + divId).showLoading();
    $.getJSON('/sistema/funcoes/pesquisaCNPJ/consultaApiClientes/?ajax=true&CNPJ=' + encodeURIComponent(CNPJ) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code){

            $("#" + divId + " #" + 'D024_Nome_Fantasia').val(request.data.D024_Nome_Fantasia);
            $("#" + divId + " #" + 'D024_Nome_Empresa').val(request.data.D024_Nome_Empresa);

            if(request.data.D024_Inscricao_Estadual)
            {
                $("#" + divId + " #" + 'D024_Inscricao_Estadual').val(request.data.D024_Inscricao_Estadual);
                $("#" + divId + " #" + 'D024_Flag_Contribuinte').val(request.data.D024_Flag_Contribuinte);
            }
            else
            {
                $("#" + divId + " #" + 'D024_Flag_Contribuinte').val(0);
                $("#" + divId + " #" + 'D024_Inscricao_Estadual').val('');
                dialogConfirm('Aviso', "Não há Inscrição Estadual para o CNPJ <b>" + CNPJ + "</b>");
            }

            reloadUI('#' . divId);
        }else{
            dialogConfirm('Erro', request.erro);
        }
    });
}

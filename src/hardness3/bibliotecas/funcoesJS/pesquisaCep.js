function pesquisaCep(idCep,divId,divIdRoot,campoCep,vars) {
    var CEP = $("#" + divId + " #" + campoCep).val();
    $('#' + divId).showLoading();
    $.getJSON('/sistema/funcoes/pesquisaCep/verificaCep/?ajax=true&CEP=' + encodeURIComponent(CEP) + '&idCep=' + encodeURIComponent(idCep) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code){
                if (typeof(vars) != 'object') {
                    vars = JSON.parse(vars);
                }
                $.each(vars, function (key, value) {
                    var retorno = key.split("_");        
                    if(retorno=='CidadeDb'){
                        $("#" + divId + " #" + value).html(request.data[key]);
                    } else {
                        $("#" + divId + " #" + value).val(request.data[key]);
                    }
                });
            reloadUI('#' . divId);
        }else{
            abrirJanela(false, divIdRoot, divId, unique(), '', 'Buscar Endereço', '/sistema/funcoes/pesquisaCep/content/','&CEP=' + encodeURIComponent(CEP) + '&camposAtualizar=' + encodeURIComponent(JSON.stringify(vars)), [990,550]);
            dialogConfirm('Erro', request.msg);
        }
    });
}
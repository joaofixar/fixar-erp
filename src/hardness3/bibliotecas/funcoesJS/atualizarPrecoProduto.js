function atualizarPrecoProduto(divId, T006_Id, T006_Valor_Preco) {
	$('#' + divId).showLoading();
    $.getJSON('/ven/ven012/form_func-ajax/atualizarPrecoProduto/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&T006_Valor_Preco=' + encodeURIComponent(T006_Valor_Preco) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code){
                divRefresh(divId, true);
        }
    });
}
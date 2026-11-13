function gerarOCProdutoPedido(divId, T174_Id, T006_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven002/grid_func-ajax/gerarOCProduto/?ajax=true&T174_Id=' + encodeURIComponent(T174_Id) + '&T006_Id=' + encodeURIComponent(T006_Id) + '&divId=' + encodeURIComponent(divId) + '&callback=?', function(request) {
		if (request.code) {
			divRefresh(divId, true);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

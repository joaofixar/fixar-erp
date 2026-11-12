function validaCampoComprar(divId, divIdRoot, T005_Id, T006_Id, D009_Id, T189_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven002/grid_func-ajax/validaCampoComprar/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&T189_Id=' + encodeURIComponent(T189_Id) + '&callback=?', function(request) {
		if (request.code) {
			abrirJanela(false, divIdRoot, divId , unique(), '', 'Comprar Produto', '/ven/ven002/content/ven002ContentComprar/', '&T005_Id=' + T005_Id + '&T006_Id=' + T006_Id + '&D009_Id=' + D009_Id, [350,250]);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

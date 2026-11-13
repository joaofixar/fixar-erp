<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraProduto(divId, D049_Id, T047_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/est/est007/form_func-ajax/est007atualizarCamposProdutos/?ajax=true&D049_Id=' + encodeURIComponent(D049_Id) + '&T047_Id=' + encodeURIComponent(T047_Id)   + '&callback=?', function(request) {
		if (request.code) {
			// Refresh no FORM
			divRefresh(divId);
			// Refresh no GRID (2rows - logo abaixo do form)
			divRefresh($('#' + divId).parent().find('.content:eq(1)').attr('id'));
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraProdutoSubItens(divId, D009_Id, T048_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/est/est007/form_func-ajax/est007atualizarCamposProdutosSubItens/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T048_Id=' + encodeURIComponent(T048_Id)   + '&callback=?', function(request) {
		if (request.code) {
			// Refresh no FORM
			divRefresh(divId);
			// Refresh no GRID (2rows - logo abaixo do form)
			divRefresh($('#' + divId).parent().find('.content:eq(1)').attr('id'));
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

} </script></div>
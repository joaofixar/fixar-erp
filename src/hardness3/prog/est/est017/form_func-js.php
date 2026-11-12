<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function est017CampoProcuraProduto(divId, D009_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad052/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #T261_D001_Id_Composicao_Pesquisa').val(request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T261_D001_Id_Composicao').val(request.data['D001_Id']);
			$('#' + divId + ' #T261_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function est017CampoProcuraProdutoOP(divId, D009_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad052/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #T256_D009_Id_Pesquisa').val(request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T256_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #T256_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function est017CampoProcuraConsumivel(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad052/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #T263_D009_Id_Pesquisa').val(request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T263_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #T263_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

} </script></div>

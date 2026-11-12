function pesquisaProdutoDefeito(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est003/form_func-ajax/produtoDefeito/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T206_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #T206_T066_Id').html(request.data['locacao']);
			// Focus
			$('#' + divId + ' #T206_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

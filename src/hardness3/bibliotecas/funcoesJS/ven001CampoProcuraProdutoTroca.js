function ven001CampoProcuraProdutoTroca(divId, D009_Id, T003_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposProdutos/?ajax=true&T003_Id=' + encodeURIComponent(T003_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		console.log(divId);
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #Procura_Produto_Troca_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T138_D009_Id').val(request.data['D009_Id']);						
			$('#' + divId + ' #Marca_Produto').val(request.data['D082_Marca']);	
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);		
			$('#' + divId + ' #T138_Valor_Preco_Unitario').val(request.data['Preco_Produto']);
			// Focus
			$('#' + divId + ' #T138_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
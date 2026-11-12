function campoProcuraProdutoDivergencia(divId, D009_Id,D144_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/est/est001/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T030_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T030_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #alterarLocacao').attr('D009_Id', request.data['D009_Id']);
			//$('#' + divId + ' #T030_Valor_Custo_Unitario_Origem').val(request.data['Custo_Original']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T030_D144_Id').val(D144_Id);
			$('#T030_T066_Id').html(request.data['T030_T066_Id']);
			$('#T030_T066_Id_Destino').html(request.data['T030_T066_Id_Destino']);
			// Focus
			$('#' + divId + ' #T030_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
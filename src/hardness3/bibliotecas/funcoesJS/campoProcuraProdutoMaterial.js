function campoProcuraProdutoMaterial(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId + ' #ven001FormMateriaisPesquisa_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T152_D037_Id').val(request.data['D001_D037_Id']); 
			//$('#' + divId + ' #T004_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T152_D009_Id').val(request.data['D009_Id']);
			//$('#' + divId + ' #T004_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			
			$('#' + divId + ' #ven001_Marca').val(request.data['D082_Marca']);
			//$('#' + divId + ' #NcmProduto').val(request.data['D001_D005_Id']);
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);			
			
			//$('#' + divId + ' #T004_Valor_Preco_Sem_Desconto_Unitario').val(request.data['D041_Valor_Base']);
			//$('#' + divId + ' #T004_IPV').val(request.data['IPV']);
			//$('#' + divId + ' #T004_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			//$('#' + divId + ' #T004_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			
			// Focus
			$('#' + divId + ' #T152_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function est015CampoProcuraProduto(divId, D009_Id, D144_Id, T218_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est015/form_func-ajax/atualizarCamposProdutos/?ajax=true&T218_Id=' + encodeURIComponent(T218_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			if(request.data['Validacao_NCM']!=""){
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['Validacao_NCM'], buttons)
			}
			// Preenche os campos
			//$('#' + divId + ' #T219_Codigo_Substituto').val(request.data['T219_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T219_D037_Id').val(request.data['D037_Id']);
			$('#' + divId + ' #T219_D005_Id').val(request.data['D005_Id']);
			$('#' + divId + ' #T219_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T219_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #D001_Id').val(request.data['D001_Id']);
			$('#' + divId + ' #T219_D006_Id').val(request.data['D006_Id']);
			$('#' + divId + ' #T219_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T219_Flag_Origem_Produto').val(request.data['D049_Origem_Mercadoria']);
			$('#' + divId + ' #T219_Flag_Destino_Produto').val(request.data['T218_Flag_Destino_Produto']);
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			//$('#' + divId + ' #NcmProduto').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #T219_Codigo_Substituto').val(request.data['Codigo_Produto_Fornecedor']);	
			
			$('#' + divId + ' #D049_Quantidade_Embalagem_Compra_disabled').val(request.data['D049_Quantidade_Embalagem_Compra']);
			
			$('#' + divId + ' #T219_Valor_Preco_Sem_Desconto_Unitario').val(request.data['Preco_Produto']);
			$('#' + divId + ' #T219_Valor_Preco_Unitario').val(request.data['Preco_Produto']);
			//$('#' + divId + ' #T219_IPV').val(request.data['IPV']);
			//$('#' + divId + ' #T219_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			$('#' + divId + ' #T219_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId + ' #T219_Aliquota_ICMS').val(request.data['Aliquota_ICM']);
			$('#' + divId + ' #UltimoPrecoCompra').val(request.data['UltimoPrecoCompra']);
			$('#' + divId + ' #T219_Flag_ST').val(request.data['Flag_ST']);
			$('#' + divId + ' #T219_ST_VA').val(request.data['MVA']);
			$('#' + divId + ' #T219_ST_ICMS_Interno').val(request.data['Aliquota_ICM_Interno']);
			$('#' + divId + ' #T219_ICMS_Inter_Estadual').val(request.data['Aliquota_ICM_Inter_Estadual']);

			$('#' + divId + ' #estoqueLiquido').val(request.data['estoqueLiquido']);
			$('#' + divId + ' #estoqueLoja').val(request.data['estoqueLoja']);
			$('#' + divId + ' #estoqueTotal').val(request.data['estoqueTotal']);
			$('#' + divId + ' #ordemCompra').val(request.data['ordemCompra']);
			$('#' + divId + ' #estoqueFora').val(request.data['estoqueFora']);
			// Focus
			$('#' + divId + ' #T219_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function ven001CampoProcuraProdutoEditar(divId, D009_Id, D144_Id, T003_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposProdutosEditar/?ajax=true&T003_Id=' + encodeURIComponent(T003_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId).parent().find('.itemForm-ven001FormProdutosEditarProcuraProdutoEditar').val(request.data['D001_Codigo_Produto']);
			$('#' + divId).parent().find('.itemForm-T004_D037_Id').val(request.data['D001_D037_Id']);
			$('#' + divId).parent().find('.itemForm-T004_D005_Id').val(request.data['D005_Id']);
			$('#' + divId).parent().find('.itemForm-T004_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId).parent().find('.itemForm-T004_D009_Id').val(request.data['D009_Id']);
			$('#' + divId).parent().find('.itemForm-T004_D082_Id').val(request.data['D082_Id']);
			$('#' + divId).parent().find('.itemForm-T004_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId).parent().find('.itemForm-Marca').val(request.data['D082_Marca']);
			$('#' + divId).parent().find('.itemForm-T004_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);			
			$('#' + divId).parent().find('.itemForm-T004_Valor_Preco_Unitario').val(request.data['Preco_Produto']);
			$('#' + divId).parent().find('.itemForm-T004_IPV').val(request.data['IPV']);
			$('#' + divId).parent().find('.itemForm-UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);

			$('#' + divId).parent().find('.itemForm-T004_Valor_Custo_Unitario').val(request.data['Custo_Produto']);
			$('#' + divId).parent().find('.itemForm-T004_Item_Pedido_Compra').val(request.data['']);
			$('#' + divId).parent().find('.itemForm-T004_D006_Id').val(request.data['D006_Id']);
			$('#' + divId).parent().find('.itemForm-T004_C004_Id').val(request.data['D009_C004_Id']);
			$('#' + divId).parent().find('.itemForm-T004_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId).parent().find('.itemForm-T004_Situacao_Tributaria_IPI').val(request.data['Situacao_Tributaria_IPI']);
			$('#' + divId).parent().find('.itemForm-T004_Aliquota_ICMS').val(request.data['Aliquota_ICMS_Correto']);
			$('#' + divId).parent().find('.itemForm-T004_Percentual_Reducao_ICMS').val(request.data['Percentual_Reducao_ICMS_Correto']);
			$('#' + divId).parent().find('.itemForm-T004_D059_Id').val(request.data['D059_Id']);
			$('#' + divId).parent().find('.itemForm-T004_ST_ICMS_Interno').val(request.data['ST_ICMS_Interno']);
			$('#' + divId).parent().find('.itemForm-T004_ST_ICMS_Interno_MVA').val(request.data['ST_ICMS_Interno_MVA']);
			$('#' + divId).parent().find('.itemForm-T004_ICMS_Inter_Estadual').val(request.data['ICMS_Inter_Estadual']);
			$('#' + divId).parent().find('.itemForm-T004_ICMS_Inter_Estadual_MVA').val(request.data['ICMS_Inter_Estadual_MVA']);
			$('#' + divId).parent().find('.itemForm-T004_ST_VA').val(request.data['ST_VA']);
			$('#' + divId).parent().find('.itemForm-T004_Aliquota_PIS').val(request.data['Percentual_PIS']);
			$('#' + divId).parent().find('.itemForm-T004_Flag_Isento_PIS').val(request.data['Flag_Isento_PIS']);
			$('#' + divId).parent().find('.itemForm-T004_Situacao_Tributaria_PIS').val(request.data['Situacao_Tributaria_PIS']);
			$('#' + divId).parent().find('.itemForm-T004_Aliquota_COFINS').val(request.data['Percentual_COFINS']);
			$('#' + divId).parent().find('.itemForm-T004_Flag_Isento_COFINS').val(request.data['Flag_Isento_COFINS']);
			$('#' + divId).parent().find('.itemForm-T004_Situacao_Tributaria_COFINS').val(request.data['Situacao_Tributaria_COFINS']);
			$('#' + divId).parent().find('.itemForm-T004_Valor_Preco_Original').val(request.data['Preco_Produto']);
			$('#' + divId).parent().find('.itemForm-T004_Demonstrativo_Preco_Venda').val(request.data['demonstrativoPrecoVenda']);
			$('#' + divId).parent().find('.itemForm-T004_Flag_ST').val(request.data['Flag_ST']);
			$('#' + divId).parent().find('.itemForm-T004_D144_Id').val(D144_Id);

			// Focus
			$('#' + divId).parent().find( + ' .itemForm-T004_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
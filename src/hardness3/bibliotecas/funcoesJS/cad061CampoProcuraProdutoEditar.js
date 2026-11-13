function cad061CampoProcuraProdutoEditar(divId, D009_Id, D144_Id, D157_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad061/form_func-ajax/CAD061atualizarCamposProdutosEditar/?ajax=true&D157_Id=' + encodeURIComponent(D157_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #D158_Codigo_Substituto').val(request.data['D158_Codigo_Substituto']);
			$('#' + divId).parent().find('.itemForm-ven002FormProdutosEditarProcuraProdutoEditar').val(request.data['D001_Codigo_Produto']);
			$('#' + divId).parent().find('.itemForm-D158_D037_Id').val(request.data['D001_D037_Id']);
			$('#' + divId).parent().find('.itemForm-D158_D005_Id').val(request.data['D005_Id']);
			$('#' + divId).parent().find('.itemForm-D158_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId).parent().find('.itemForm-D158_D009_Id').val(request.data['D009_Id']);
			$('#' + divId).parent().find('.itemForm-D158_D082_Id').val(request.data['D082_Id']);
			$('#' + divId).parent().find('.itemForm-D158_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId).parent().find('.itemForm-Marca').val(request.data['D082_Marca']);
			$('#' + divId).parent().find('.itemForm-D158_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);			
			$('#' + divId).parent().find('.itemForm-D158_Valor_Preco_Sem_Desconto_Unitario').val(request.data['Preco_Produto']);
			$('#' + divId).parent().find('.itemForm-D158_IPV').val(request.data['IPV']);
			$('#' + divId).parent().find('.itemForm-UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);

			$('#' + divId).parent().find('.itemForm-D158_Valor_Custo_Unitario').val(request.data['D009_Valor_Custo_Unitario']);
			$('#' + divId).parent().find('.itemForm-D158_Item_Pedido_Compra').val(request.data['']);
			$('#' + divId).parent().find('.itemForm-D158_D006_Id').val(request.data['D006_Id']);
			$('#' + divId).parent().find('.itemForm-D158_C004_Id').val(request.data['D009_C004_Id']);
			$('#' + divId).parent().find('.itemForm-D158_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId).parent().find('.itemForm-D158_Situacao_Tributaria_IPI').val(request.data['Situacao_Tributaria_IPI']);
			$('#' + divId).parent().find('.itemForm-D158_Aliquota_ICMS').val(request.data['Aliquota_ICMS_Correto']);
			$('#' + divId).parent().find('.itemForm-D158_Percentual_Reducao_ICMS').val(request.data['Percentual_Reducao_ICMS_Correto']);
			$('#' + divId).parent().find('.itemForm-D158_D059_Id').val(request.data['D059_Id']);
			$('#' + divId).parent().find('.itemForm-D158_ST_ICMS_Interno').val(request.data['ST_ICMS_Interno']);
			$('#' + divId).parent().find('.itemForm-D158_ST_ICMS_Interno_MVA').val(request.data['ST_ICMS_Interno_MVA']);
			$('#' + divId).parent().find('.itemForm-D158_ICMS_Inter_Estadual').val(request.data['ICMS_Inter_Estadual']);
			$('#' + divId).parent().find('.itemForm-D158_ICMS_Inter_Estadual_MVA').val(request.data['ICMS_Inter_Estadual_MVA']);
			$('#' + divId).parent().find('.itemForm-D158_ST_VA').val(request.data['ST_VA']);
			$('#' + divId).parent().find('.itemForm-D158_Aliquota_PIS').val(request.data['Percentual_PIS']);
			$('#' + divId).parent().find('.itemForm-D158_Flag_Isento_PIS').val(request.data['Flag_Isento_PIS']);
			$('#' + divId).parent().find('.itemForm-D158_Situacao_Tributaria_PIS').val(request.data['Situacao_Tributaria_PIS']);
			$('#' + divId).parent().find('.itemForm-D158_Aliquota_COFINS').val(request.data['Percentual_COFINS']);
			$('#' + divId).parent().find('.itemForm-D158_Flag_Isento_COFINS').val(request.data['Flag_Isento_COFINS']);
			$('#' + divId).parent().find('.itemForm-D158_Situacao_Tributaria_COFINS').val(request.data['Situacao_Tributaria_COFINS']);
			$('#' + divId).parent().find('.itemForm-D158_Valor_Preco_Original').val(request.data['Preco_Produto']);
			$('#' + divId).parent().find('.itemForm-D158_Flag_ST').val(request.data['Flag_ST']);
			$('#' + divId).parent().find('.itemForm-D158_D144_Id').val(D144_Id);

			// Focus
			$('#' + divId).parent().find( + ' .itemForm-D158_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
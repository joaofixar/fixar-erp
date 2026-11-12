<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function campoProcuraProduto(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est006/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T012_Codigo_Substituto').val(request.data['T012_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T012_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T012_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T012_D009_Id').val(request.data['D009_Id']);
			
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			
			// Focus
			$('#' + divId + ' #T012_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function est006ProcuraFornecedor(divId, D024_Id) {
		//divSetarAcaoId(divId, D009_Id, 'D009_Id');
		$('#' + divId).showLoading();
		$.getJSON('/est/est006/form_func-ajax/atualizarCamposFornecedores/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
			if(request.code) {
				// Preenche os campos
				//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
				$('#' + divId + ' #Procura_Fornecedor_Pesquisa').val(request.data);
				$('#' + divId + ' #T011_D024_Id').val(D024_Id);
				// Focus
				$('#' + divId + ' #T011_C007_Id').focus();
			}
		});      
		$('#' + divId).hideLoading();
	}
} </script></div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function campoProcuraProduto(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est008/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T012_Codigo_Substituto').val(request.data['T012_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T059_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T059_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T059_D009_Id').val(request.data['D009_Id']);
			
			$('#' + divId + ' #D082_Marca').val(request.data['D082_Marca']);
			
			// Focus
			$('#' + divId + ' #T059_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraProdutoFornecedor(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est008/form_func-ajax/atualizarCamposProdutosFornecedores/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T012_Codigo_Substituto').val(request.data['T012_Codigo_Substituto']);
			$('#' + divId + ' .itemForm-Procura_Produto_Fornecedor_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' .itemForm-T059_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' .itemForm-T059_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' .itemForm-T059_D009_Id').val(request.data['D009_Id']);
			
			$('#' + divId + ' .itemForm-D082_Marca').val(request.data['D082_Marca']);
			
			// Focus
			$('#' + divId + ' .itemForm-T059_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function est008campoProcuraProdutoCotacaoForn(divId, D009_Id, T010_Id, T173_Id, div) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est008/form_func-ajax/atualizarCamposProdutosCotacaoForn/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T010_Id=' + encodeURIComponent(T010_Id) + '&T173_Id=' + encodeURIComponent(T173_Id) + '&callback=?', function(request) {
		if (request.code) {
			divRefresh(div, true);
			$("#" + divId).closest('.ui-dialog-content').dialog('close');
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

	function est008ProcuraFornecedor(divId, D024_Id) {
		//divSetarAcaoId(divId, D009_Id, 'D009_Id');
		$('#' + divId).showLoading();
		$.getJSON('/est/est008/form_func-ajax/atualizarCamposFornecedores/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
			if(request.code) {
				// Preenche os campos
				//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
				$('#' + divId + ' #Procura_Fornecedor_Pesquisa').val(request.data['D024_Nome_Empresa']);
				$('#' + divId + ' #T173_D024_Id').val(D024_Id);
				$('#' + divId + ' #T173_D013_Id').html(request.data['T173_D013_Id']);
				var refreshSelect = request.data['T173_D013_Id_refreshBtn'];
				$('#' + divId + ' #T173_D013_Id_refreshBtn').attr('onclick', "gRecarregaSelect('" + divId+ "', 'T173_D013_Id', '"+refreshSelect+"');");
				// Focus
				$('#' + divId + ' #T173_Frete').focus();
			}
		});      
		$('#' + divId).hideLoading();
	} 
}</script></div>

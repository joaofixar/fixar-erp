<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function campoProcuraProdutoAssociar(divId, D009_Id, T189_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est014/form_func-ajax/atualizarCamposProdutosAssociar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Associar_Pesquisa').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #D001_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #D001_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			//$('#' + divId + ' #T004_D037_Id').val(request.data['D001_D037_Id']);
			//$('#' + divId + ' #T004_Valor_Preco_Sem_Desconto_Unitario').val(request.data['D041_Valor_Base']);
			//$('#' + divId + ' #T004_IPV').val(request.data['IPV']);
			
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraProdutoRequisitar(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est014/form_func-ajax/atualizarCamposProdutosRequisitar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T012_Codigo_Substituto').val(request.data['T012_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T189_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T059_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T189_D009_Id').val(request.data['D009_Id']);
			
			$('#' + divId + ' #D082_Marca').val(request.data['D082_Marca']);
			
			// Focus
			$('#' + divId + ' #Procura_Fornecedor_Pesquisa').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function est014ProcuraFornecedor(divId, D024_Id) {
		//divSetarAcaoId(divId, D009_Id, 'D009_Id');
		$('#' + divId).showLoading();
		$.getJSON('/est/est014/form_func-ajax/est014atualizarCamposFornecedores/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
			if(request.code) {
				// Preenche os campos
				//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
				$('#' + divId + ' #Procura_Fornecedor_Pesquisa').val(request.data);
				$('#' + divId + ' #Procura_Fornecedor').val(request.data);
				$('#' + divId + ' #T189_D024_Id').val(D024_Id);
				// Focus
				$('#' + divId + ' #T189_Quantidade').focus();
			}
		});      
		$('#' + divId).hideLoading();
	} 

function campoProcuraProdutoOS(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est014/form_func-ajax/atualizarCamposProdutosRequisitar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T012_Codigo_Substituto').val(request.data['T012_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T129_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T129_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T129_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #T129_Quantidade_Estoque').val(request.data['D009_Quantidade_Estoque']);
			
			$('#' + divId + ' #D082_Marca').val(request.data['D082_Marca']);
			
			// Focus
			$('#' + divId + ' #T129_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
} </script></div>
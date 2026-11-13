<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function crm005CampoProcuraProduto(divId, D009_Id, D024_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/crm/crm005/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			var produto = request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto'] + ' - ' + request.data['D082_Marca']; 
			$('#' + divId + ' #Procura_ProdutoPrePedido_Pesquisa').val(produto);
			$('#' + divId + ' #T099_Valor_Preco_Unitario').val(request.data['D049_Preco_1']);
			$('#' + divId + ' #T099_D037_Id').val(request.data['D001_D037_Id']);
			$('#' + divId + ' #T099_D009_Id').val(encodeURIComponent(D009_Id));
			$('#' + divId + ' #T099_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function crm005CampoProcuraProdutoCRM(divId, D009_Id, T003_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/crm/crm005/form_func-ajax/atualizarCamposProdutos/?ajax=true&T003_Id=' + encodeURIComponent(T003_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			var produto = request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto'] + ' - ' + request.data['D082_Marca']; 
			$('#' + divId + ' #Procura_ProdutoCRM_Pesquisa').val(produto);
			$('#' + divId + ' .itemForm-T099_Valor_Preco_Unitario').val(request.data['D041_Valor_Base']);
			$('#' + divId + ' .itemForm-T099_D037_Id').val(request.data['D001_D037_Id']);
			$('#' + divId + ' .itemForm-T099_D009_Id').val(encodeURIComponent(D009_Id));
			$('#' + divId + ' .itemForm-T099_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function crm005CampoProcuraCliente(divId, D024_Id, formIdCliente, divContent) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/crm/crm005/form_func-ajax/atualizarCamposCliente/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			$('#' + formIdCliente + ' .itemForm-T099_D024_Id').val(request.data['D024_Id']);
			$('#' + divId + ' .itemForm-Procura_Produto_Pesquisa').val(request.data['D024_Nome_Fantasia']);

			$('#' + divId + ' .itemForm-Bairro').val(request.data['D024_Bairro']);
			$('#' + divId + ' .itemForm-DDD_Telefone').val(request.data['D024_DDD_Telefone_1']);
			$('#' + divId + ' .itemForm-Telefone').val(request.data['D024_Telefone_1']);
			$('#' + divId + ' .itemForm-Nome_Fantasia').val(request.data['D024_Nome_Fantasia']);
			divSetarAcaoId(divContent, D024_Id, 'D024_Id');
			divRefresh(divContent, true);
			setTimeout(function () {	
				$('#' + formIdCliente + '#Procura_ProdutoPrePedido_Pesquisa').focus();
			}, 180);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

} </script></div>
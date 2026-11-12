<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function cad002CampoProcuraProduto(divId, D049_Id_Itens, D049_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad002/form_func-ajax/cad002atualizarCamposProdutos/?ajax=true&D049_Id_Itens=' + encodeURIComponent(D049_Id_Itens) + '&D049_Id=' + encodeURIComponent(D049_Id)   + '&callback=?', function(request) {
		if (request.code) {
			// Refresh no FORM
			divRefresh(divId);
			// Refresh no GRID (2rows - logo abaixo do form)
			setTimeout(function(){
				divRefresh($('#' + divId).parent().parent().find('.content:eq(1)').attr('id'));
			}, 200)
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function cad002ProcurarJuntarProdutos(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad002/form_func-ajax/cad002atualizarCamposJuntarProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
				// Preenche os campos
				//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
				$('#' + divId + ' #D001_D001_Id_Antigo').val(request.data['D001_Id']);
				$('#' + divId + ' #Consulta_D001_D001_Id_Antigo').val(request.data['D001_Codigo_Produto']);
				$('#' + divId + ' #Consulta_D001_D001_Id_Antigo_Pesquisa').val(request.data['D001_Codigo_Produto']);

				// Focus
				$('#' + divId + ' #Consulta_D001_D001_Id_Novo_Pesquisa').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function cad002ProcurarJuntarProdutos2(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad002/form_func-ajax/cad002atualizarCamposJuntarProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
				$('#' + divId + ' #D001_D001_Id_Novo').val(request.data['D001_Id']);
				$('#' + divId + ' #Consulta_D001_D001_Id_Novo').val(request.data['D001_Codigo_Produto']);
				$('#' + divId + ' #Consulta_D001_D001_Id_Novo_Pesquisa').val(request.data['D001_Codigo_Produto']);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraProdutoAssociar(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad002/form_func-ajax/atualizarCamposProdutosAssociar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Associar_Pesquisa').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #D001_Codigo_Produto_Associar').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #D009_Id').val(request.data['D009_Id']);
			//$('#' + divId + ' #T014_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
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
function cad002AssociarProduto(divId, D069_Id, D009_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad002/form_func-ajax/atualizarCamposProdutosAssociarInverso/?ajax=true&D069_Id=' + encodeURIComponent(D069_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #ProcuraProduto_Pesquisa').val(request.data['D069_Descricao_Produto']);
			$('#' + divId + ' #D069_Id').val(D069_Id);
			$('#' + divId + ' #D009_Id').val(D009_Id);
			//$('#' + divId + ' #T014_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
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

function cad002MudarC008(divId, elem) {
	$('#' + divId).showLoading();
    $.getJSON('/cad/cad002/form_func-ajax/mudarC008/?ajax=true&C008_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D001_D060_Id').html(request.data['D001_D060_Id']);
			var D001_D060_Id_refreshBtn = $('#D001_D060_Id_refreshBtn').attr('onclick');
			var matches = D001_D060_Id_refreshBtn.match(/, '[^']+'/g);
			D001_D060_Id_refreshBtn = D001_D060_Id_refreshBtn.replace(matches[1], ", '" + request.data['D001_D060_Id_refreshBtn'] + "'");
			$('#D001_D060_Id_refreshBtn').attr('onclick', D001_D060_Id_refreshBtn);
		}
	});
}

function cad002IncluirProdutoFornecedor(divId, D009_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad002/form_func-ajax/atualizarCamposIncluirProdutoFornecedor/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #ProcuraProduto_Pesquisa').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #D069_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #D069_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #D069_D009_Id').val(D009_Id);			
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function cad002MudarC008Autocomplete(divId, elem) {
	$('#' + divId).showLoading();
	$('#' + divId + ' #D001_D060_Id_Pesquisa').autocomplete({source: function(request, response){
		$.ajax({
			url: "/sistema/funcoes/pesquisa/campoPesquisa/",
			dataType: "jsonp",
			data: 'ajax=true&tabela=' + encodeURIComponent('D060') + '&campoId=' + encodeURIComponent('D060_Id') + '&campoTxt=' + encodeURIComponent('D060_Id, D060_Descricao_Grupo') + '&sqlExtra=' + encodeURIComponent('WHERE (D060_C008_Id = \'' + $('#D001_C008_Id').val() + '\' OR IFNULL(D060_C008_Id,0) <= 0) order by D060_Id') + '&formato=' + encodeURIComponent('%1 %2') + '&pesquisa=' + encodeURIComponent(request.term),
			success: function( data ) {
				if (isempty(data)) {
					data = [{
						title: 'Não encontrado! Pesquise novamente.',
						value: 0
					}];
				}
				response( $.map( data, function( item ) {
					return {
						label: item.title,
						value: item.value
					}
				}));
			}
		});
	},
	delay: 200,
	select: function(event, ui) { 
		// Ao selecionar, seta o campo hidden ID
		var id = $(this).attr('id').replace(/\_Pesquisa$/, '');
		if (ui.item.value != 0) {
			$(this).val(ui.item.label);
			$('#' + id).val(ui.item.value);
			$(this).data('selectLabel', ui.item.label);
			$(this).data('selectValue', ui.item.value);
		} else {
			$(this).val('');
			$('#' + id).val('');
		}
		$(this).trigger('change');
		return false;
	},
	change: function(event, ui) {
		// Usuário não pode mudar o campo, apenas selecionar um valor da pesquisa
		var id = $(this).attr('id').replace(/\_Pesquisa$/, '');
		if (isempty($(this).val())) {
			$(this).val('');
			$('#' + id).val('');
			$(this).data('selectLabel', '');
			$(this).data('selectValue', '');
		} else if ($(this).val() != $(this).data('selectLabel')) {
			$(this).val($(this).data('selectLabel'));
			$('#' + id).val($(this).data('selectValue'));
		}
		return false;
		},
	focus: function(event, ui) { 
		$(this).val(ui.item.label);
		return false;
	}
	}).data('selectLabel', '').data('selectValue', 0);;
	$('#' + divId).hideLoading();
}

} </script></div>
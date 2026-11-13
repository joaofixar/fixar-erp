<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraProduto(divId, D009_Id,D144_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
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

function campoProcuraServico(divId, D112_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposServicos/?ajax=true&D112_Id=' + encodeURIComponent(D112_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Servico_Pesquisa').val(request.data['D112_Id']);
			$('#' + divId + ' #T145_Titulo').val(request.data['D112_Titulo']);
			$('#' + divId + ' #T145_Descricao').val(request.data['D112_Descricao']);
			$('#' + divId + ' #T145_Tempo_Estimado').val(request.data['D112_Tempo_Estimado']);
			$('#' + divId + ' #T145_Valor_Unitario').val(request.data['D112_Valor_Unitario']);
			$('#' + divId + ' #T145_D112_Id').val(request.data['D112_Id']);
			$('#' + divId + ' #T145_D110_Id_Pesquisa').val(request.data['D110_Titulo']);
			$('#' + divId + ' #T145_D110_Id').val(request.data['D112_D110_Id']);
			
			// Focus
			$('#' + divId + ' #T145_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function insereRegistroT001(T030_Id,divId){
	var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est001/form_func-ajax/insereRegistroT001/?ajax=true&T030_Id=' + encodeURIComponent(T030_Id) + '&callback=?', function(request) {
				if (request.code) {
					divRefresh(divId);
					var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Ok', request.data, buttons);    		
				} else {
					var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Erro', request.data, buttons);
				}
				$('#' + divId).hideLoading();
				reloadUI(divId);
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Inserir T001', 'Confirma a inclusão do registro no T001?', buttons);
}

} </script></div>
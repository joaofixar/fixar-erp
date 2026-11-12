<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraBem(divId, D106_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad040/form_func-ajax/atualizarCamposBens/?ajax=true&D106_Id=' + encodeURIComponent(D106_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Bem_Pesquisa').val(request.data['D106_Descricao']);
			$('#' + divId + ' #D125_D106_Id').val(request.data['D106_Id']);
			
			// nao funciona direito, consertar para carregar todos os contratos deste produto
			//$('#' + divId + ' #D125_T141_Id').html(request.data['D125_T141_Id']);
			
			
			// Focus
			$('#' + divId + ' #D125_Observacao').focus();
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

} </script></div>
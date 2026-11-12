<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraProdutoNota(divId, D009_Id, divReload, acaoId) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est004/form_func-ajax/atualizarCamposProdutosNota/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T013_Id=' + encodeURIComponent(acaoId) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T014_D009_Id').val(request.data['D009_Id']);		
			$('#' + divId + ' #T014_D006_Id').html(request.data['D006_Id']);		
			
			//Locação
			$('#' + divId + ' #T014_T066_Id').html(request.data['T014_T066_Id']);
			var T014_T066_Id_refreshBtn = $('#' + divId + ' #T014_T066_Id_refreshBtn').attr('onclick');
			var matches = T014_T066_Id_refreshBtn.match(/, '[^']+'/g);
			T014_T066_Id_refreshBtn = T014_T066_Id_refreshBtn.replace(matches[1], ", '" + request.data['T014_T066_Id_refreshBtn'] + "'");
			$('#' + divId + ' #T014_T066_Id_refreshBtn').attr('onclick', T014_T066_Id_refreshBtn);			

			divSetarAcaoId(divId, D009_Id, 'idD009');
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function campoProcuraProdutoAssociar(divId, T008_Id, T014_Id) {
	//divSetarAcaoId(divId, T008_Id, 'T008_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est004/form_func-ajax/atualizarCamposProdutosAssociar/?ajax=true&T008_Id=' + encodeURIComponent(T008_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Associar_Pesquisa').val(request.data['T008_Codigo_Produto'] + ' - ' + request.data['T008_Descricao_Produto']);
			$('#' + divId + ' #T008_Codigo_Produto').val(request.data['T008_Codigo_Produto']);
			$('#' + divId + ' #D009_Id').val(request.data['T008_D009_Id']);
			$('#' + divId + ' #T008_Id').val(request.data['T008_Id']);			
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function campoProcuraProdutoAssociarItem(divId, D009_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/est/est004/form_func-ajax/campoProcuraProdutoAssociarItem/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Associar_Pesquisa').val(request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #D001_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #D009_Id').val(request.data['D009_Id']);			
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function preencheNaoTem(){
	$('#T013_Numero_Conhecimento').val("NAO TEM");
}

} </script></div>
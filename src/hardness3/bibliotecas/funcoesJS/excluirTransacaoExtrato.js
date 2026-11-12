function excluirTransacaoExtrato(T216_Id, T216_Flag_Excluido, divId){
	var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/fin/fin003/grid_func-ajax/excluirTransacaoExtrato/?ajax=true&T216_Id=' + encodeURIComponent(T216_Id) + '&T216_Flag_Excluido=' + encodeURIComponent(T216_Flag_Excluido) + '&callback=?', function(request) {
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
	if(T216_Flag_Excluido == 'N'){
		var texto = 'Deseja excluir esta transação?'
	} else {
		var texto = 'Deseja recuperar esta transação?'
	}
	dialogConfirm('Atenção', texto, buttons);
}
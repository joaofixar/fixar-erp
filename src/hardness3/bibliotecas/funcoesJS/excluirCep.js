function excluirCep(W004_Id,divId){
	var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/fis/fis005/grid_func-ajax/excluirCep/?ajax=true&W004_Id=' + encodeURIComponent(W004_Id) + '&callback=?', function(request) {
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
	dialogConfirm('Excluir Cep', 'Confirma a exclusão do Cep?', buttons);
}
function campoOpcoesRetornarColeta(T224_Id, flag_status, divId) {
    var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est003/grid_func-ajax/mudarStatusColeta/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&flag_status=' + encodeURIComponent(flag_status) + '&callback=?', function(request) {
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
	dialogConfirm('Coleta', 'Deseja retornar a ordem de compra?', buttons);
}
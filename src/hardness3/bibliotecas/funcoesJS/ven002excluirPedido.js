function ven002excluirPedido(T005_Id,divId){
	var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/ven/ven002/grid_func-ajax/excluirPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
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
	dialogConfirm('Excluir Pedido', 'Confirma a exclusão do Pedido?', buttons);
}
function est002RetornarPedidoColetado(T005_Id, divId, T005_Flag_Status){
	T005_Flag_Status = (typeof(T005_Flag_Status) == 'undefined') ? 0 : T005_Flag_Status;
	var $buttons = {
		"Sim": function() {
			//var T005_Id = $('#' + divId + ' #T005_Id').val();
			$('#' + divId).showLoading();
			$.getJSON('/est/est002/grid_func-ajax/retornarPedidoColetado/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&T005_Flag_Status=' + encodeURIComponent(T005_Flag_Status) + '&callback=?', function(request) {
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Ok', 'Pedido retornado!', $buttons);
				    divRefresh(divId, true);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Erro', request.data, $buttons);
				}
				$('#' + divId).hideLoading();
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Atenção!!', 'Confirma retorno do pedido '+T005_Id+' para Aguardando Coleta?', $buttons);
}
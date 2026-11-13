<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function est017CancelarOrdemProducao(T255_Id, divId) {
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est017/grid_func-ajax/cancelarOrdemProducao/?ajax=true&T255_Id=' + encodeURIComponent(T255_Id) + '&callback=?', function(request) {
				$('#' + divId).hideLoading();
				if (request.code) {
					divRefresh(divId);
				} else {
					dialogConfirm('Erro', request.data, {"Fechar": function() { $(this).dialog("destroy").remove(); }});
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); }
	};
	dialogConfirm('Cancelar ordem de produção', 'Deseja realmente cancelar esta ordem de produção?', $buttons);
}

function est017ExcluirOrdemProducao(T255_Id, divId) {
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est017/grid_func-ajax/excluirOrdemProducao/?ajax=true&T255_Id=' + encodeURIComponent(T255_Id) + '&callback=?', function(request) {
				$('#' + divId).hideLoading();
				if (request.code) {
					divRefresh(divId);
				} else {
					dialogConfirm('Erro', request.data, {"Fechar": function() { $(this).dialog("destroy").remove(); }});
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); }
	};
	dialogConfirm('Excluir ordem de produção', 'Deseja realmente excluir esta ordem de produção?', $buttons);
}

} </script></div>

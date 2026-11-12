function excluirAgregadoOrdemCompra(T210_Id, divId) {
	var $buttons = {
		"Deletar": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est006/grid_func-ajax/excluirAgregadoOrdemCompra/?ajax=true&T210_Id=' + encodeURIComponent(T210_Id) + '&callback=?', function(request) {
				$('#' + divId).hideLoading();
				if (request.code) {
					divRefresh(divId);
				} else {
					var $buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
					dialogConfirm('Erro ao Excluir Item', request.data, $buttons2);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Deseja Remover?', "Atenção! Você deseja realmente remover este item?", $buttons);
}
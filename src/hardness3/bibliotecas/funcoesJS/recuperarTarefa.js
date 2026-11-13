function recuperarTarefa(T259_Id, divId) {
	var $buttons = {
		"Deletar": function() {
			$('#' + divId).showLoading();
			$.getJSON('/etc/etc017/grid_func-ajax/recuperarTarefa/?ajax=true&T259_Id=' + encodeURIComponent(T259_Id) + '&callback=?', function(request) {
				$('#' + divId).hideLoading();
				if (request.code) {
					$('[id*="T259_Id-' + T259_Id + '"]').remove();
					reloadUI(divId);
				} else {
					var $buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
					dialogConfirm('Erro ao Excluir Item', request.data, $buttons2);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Atenção!', "Você deseja recuperar tarefa da lixeira?", $buttons);
}
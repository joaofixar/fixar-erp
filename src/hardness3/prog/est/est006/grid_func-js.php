<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function est006CampoExclusao(T011_Id, divId) {
		var $buttons = {
			"Deletar": function() {
				$('#' + divId).showLoading();
				$.getJSON('/est/est006/grid_func-ajax/excluir/?ajax=true&T011_Id=' + encodeURIComponent(T011_Id) + '&callback=?', function(request) {
					if (request.code) {
						divRefresh(divId, true);
					} else {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Erro', request.data, $buttons);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao do registro??", $buttons);
}

} </script></div>
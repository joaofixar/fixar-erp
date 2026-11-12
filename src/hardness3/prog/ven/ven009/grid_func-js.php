<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)
function ven009CampoExclusao(T143_Id, divId) {
    if (T143_Id > 0)
    {
    	var $buttons = {
			"Deletar": function() {
				var $buttons = {
					"Deletar": function() {
						// AJAX
              			$('#' + divId).showLoading();
              			$.getJSON('/ven/ven009/grid_func-ajax/excluir/?ajax=true&T143_Id=' + encodeURIComponent(T143_Id) + '&callback=?', function(request) {
              				$('#' + divId).hideLoading();
							if (request.code) {
								$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
								reloadUI(divId);							// atualiza elementos (tabulação do grid)
							} else {
								alert(request.data);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Cancelar": function() { $(this).dialog("destroy").remove(); }
				}
    			dialogConfirm('Tem certeza?', "Tem certeza mesmo?", $buttons);
    			$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Remover?', "ATENÇÃO! Confirma a exclusao do registro?", $buttons);
	}
    else
    {
    	var $buttons = {
			"Deletar": function() {
				var $buttons = {
					"Deletar": function() {
             			$('#' + divId).showLoading();
             			$.getJSON('/ven/ven009/grid_func-ajax/excluir/?ajax=true&T143_Id=' + encodeURIComponent(T143_Id) + '&callback=?', function(request) {
              				$('#' + divId).hideLoading();
							if (request.code) {
								$('#' + request.data).remove();				// apaga linha
								reloadUI(divId);							// atualiza elementos (tabulação do grid)
							} else {
								alert(request.data);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Cancelar": function() { $(this).dialog("destroy").remove(); }
				}
    			dialogConfirm('Tem certeza?', "ATENCAO! Confirma a exclusao do registro?", $buttons);
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Remover?', "ATENÇÃO! Confirma a exclusao do registro?", $buttons);
    }
}

function ven009AbreContrato(T143_T141_Id, divId) {

}
} </script></div>
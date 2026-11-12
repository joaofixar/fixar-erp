<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad011CampoExclusao(D110_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/cad/cad011/grid_func-ajax/excluir/?ajax=true&D110_Id=' + encodeURIComponent(D110_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="d110 _Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Competência excluída");
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao da competência??", $buttons);
}
} </script></div>
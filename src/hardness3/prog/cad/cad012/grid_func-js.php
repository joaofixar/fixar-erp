<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad012CampoExclusao(D111_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/cad/cad012/grid_func-ajax/excluir/?ajax=true&D111_Id=' + encodeURIComponent(D111_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="d110 _Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Registro excluído");
					} else {
						alert(request.data);
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
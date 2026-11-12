<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function ven011CampoExclusao(T142_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/ven/ven011/grid_func-ajax/excluir/?ajax=true&T142_Id=' + encodeURIComponent(T142_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T142 _Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Evento excluído");
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao da manutenção??", $buttons);
}
} </script></div>
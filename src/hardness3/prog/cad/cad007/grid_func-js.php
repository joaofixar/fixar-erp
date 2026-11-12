<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad007CampoExclusao(T140_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/cad/cad007/grid_func-ajax/excluir/?ajax=true&T140_Id=' + encodeURIComponent(T140_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T140 _Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Certificado excluído");
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao do certificado??", $buttons);
}
} </script></div>
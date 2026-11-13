<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad009CampoExclusao(D107_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/cad/cad009/grid_func-ajax/excluir/?ajax=true&D107_Id=' + encodeURIComponent(D107_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="D107 _Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Bem excluído");
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao de bem??", $buttons);
}

function cad009campoExclusaoItem(D108_Id,D108_D107_Id, divId) 
{
	var $buttons = {
		"Deletar": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/cad/cad009/grid_func-ajax/excluirItem/?ajax=true&D108_Id=' + encodeURIComponent(D108_Id) +'&D108_D107_Id='+ encodeURIComponent(D108_D107_Id) + '&callback=?', function(request) {
  			$('#' + divId).hideLoading();
				if (request.code) {
					$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="D107 _Id-*")
					divRefresh(divId);				// atualiza elementos (tabulação do grid)
					alert("Ítem excluído");
				} else {
					alert(request.data);
				}				
			});
			$(this).dialog("destroy").remove();
		},
		"Cancelar": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao do item?", $buttons);
}
} </script></div>
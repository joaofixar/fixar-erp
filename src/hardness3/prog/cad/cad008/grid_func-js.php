<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad008CampoExclusao(D106_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/cad/cad008/grid_func-ajax/excluir/?ajax=true&D106_Id=' + encodeURIComponent(D106_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="D106 _Id-*")
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

function cad008CampoExcluirManutencao(T142_Id, divId){
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

function cad008CampoExcluirLogistica(T143_Id, divId) 
{
	var $buttons = {
		"Deletar": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/ven/ven009/grid_func-ajax/excluir/?ajax=true&T143_Id=' + encodeURIComponent(T143_Id) + '&callback=?', function(request) {
				if (request.code) {
					$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T143 _Id-*")
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

function cad008CheckboxChecklist(checkbox,D120_Id,D106_Id,divId){
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
	   
		$('#' + divId).showLoading();
		$.getJSON('/cad/cad008/grid_func-ajax/gravarCampo/?ajax=true&D120_Id=' + encodeURIComponent(D120_Id) + '&D106_Id=' + encodeURIComponent(D106_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();		
		});
}
} </script></div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function ven010CampoExclusao(T141_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/ven/ven010/grid_func-ajax/excluir/?ajax=true&T141_Id=' + encodeURIComponent(T141_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T141 _Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Contrato de locação excluído");
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Excluir?', "ATENCAO! Confirma a exclusao do contrato??", $buttons);
}

function VEN010GerarContasReceber(T141_Id,divId){
	var $buttons = {
			"Confirmar": function() {
      			$.getJSON('/ven/ven010/grid_func-ajax/gerarContasReceber/?ajax=true&T141_Id=' + encodeURIComponent(T141_Id) + '&callback=?', function(request) {
					if (request.code) {
						divRefresh(divId);							// atualiza elementos (tabulação do grid)
						alert("Contas a receber geradas com sucesso!");
					} else {
						alert(request.data);
					}					
				});
				$(this).dialog("destroy").remove();
			},	
			"Cancelar": function() { $(this).dialog("destroy").remove(); }		
		}
    	dialogConfirm('Gerar Recebimentos', "Confirma geração de contas a receber?", $buttons);		
}
} </script></div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function imp004CampoExclusaoReceber(T002_Id, T035_Id, divId) {
    if (T035_Id > 0)
    {
    	var $buttons = {
			"Deletar": function() {
				var $buttons = {
					"Deletar": function() {
						// AJAX
						//parent.MostrarMensagem('inline');
              			//document.getElementById('fme_gravar').src='excluir.php?T002_Id='+T002_Id+'&T035_Id='+T035_Id;
              			$('#' + divId).showLoading();
              			$.getJSON('/fin/fin001/grid_func-ajax/excluir/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&T035_Id=' + encodeURIComponent(T035_Id) + '&callback=?', function(request) {
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
    	dialogConfirm('Deseja Remover?', "ATENCAO! Esse titulo foi enviado para banco na remessa " + T035_Id + "!", $buttons);
	}
    else
    {
    	var $buttons = {
			"Deletar": function() {
				var $buttons = {
					"Deletar": function() {
						// sistema antigo:
						//parent.MostrarMensagem('inline');
             			//document.getElementById('fme_gravar').src='excluir.php?T002_Id='+T002_Id;
             			$('#' + divId).showLoading();
              			$.getJSON('/fin/fin001/grid_func-ajax/excluir/?ajax=true&T002_Id=' + encodeURIComponent(T002_Id) + '&T035_Id=' + encodeURIComponent(T035_Id) + '&callback=?', function(request) {
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
    			dialogConfirm('Tem certeza?', "ATENCAO! Confirma a exclusao do titulo?", $buttons);
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Remover?', "ATENCAO! Confirma a exclusao do titulo?", $buttons);
    }
}

function checkboxExcluirItemQuotation(checkbox,T294_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/imp/imp004/grid_func-ajax/checkboxExcluirItemQuotation/?ajax=true&T294_Id=' + encodeURIComponent(T294_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}
 
} </script></div>
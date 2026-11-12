<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function imp005CampoExclusaoReceber(T002_Id, T035_Id, divId) {
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

function checkboxExcluirItemPO(checkbox,T296_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/imp/imp005/grid_func-ajax/checkboxExcluirItemPO/?ajax=true&T296_Id=' + encodeURIComponent(T296_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}
 
function cancelarPO(T295_Id, divId) {
	var $buttons = {
		"Cancelar": function() {
			$('#' + divId).showLoading();
			$.getJSON('/imp/imp005/grid_func-ajax/cancelarPO/?ajax=true&T295_Id=' + encodeURIComponent(T295_Id) + '&callback=?', function(request) {
				$('#' + divId).hideLoading();
				if (request.code) {
					divRefresh(divId);
				} else {
					dialogConfirm('Erro!', request.data);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Fechar": function() { $(this).dialog("destroy").remove(); }
	}
	dialogConfirm('Cancelar Purchase Order', "Deseja realmente cancelar a Purchase Order?", $buttons);
	$(this).dialog("destroy").remove();	
}

} </script></div>
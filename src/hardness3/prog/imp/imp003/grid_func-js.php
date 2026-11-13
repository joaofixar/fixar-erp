<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function imp003CampoExclusao(T015_Id, divId) 
    {
    	var $buttons = {
			"Deletar": function() {
      			$('#' + divId).showLoading();
      			$.getJSON('/fin/fin002/grid_func-ajax/excluir/?ajax=true&T015_Id=' + encodeURIComponent(T015_Id) + '&callback=?', function(request) {
					if (request.code) {
						$('#' + request.data).remove();				// apaga linha (cada elemento <tr> tem o id="T002_Id-*")
						reloadUI(divId);				// atualiza elementos (tabulação do grid)
						alert("Título excluído");
					} else {
						alert(request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		}
    	dialogConfirm('Deseja Remover?', "ATENCAO! Confirma a exclusao do titulo??", $buttons);
}

function imp003CampoExclusaoReceber(T002_Id, T035_Id, divId) {
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

function checkboxExcluirItemDI(checkbox,T055_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/imp/imp003/grid_func-ajax/checkboxExcluirItemDI/?ajax=true&T055_Id=' + encodeURIComponent(T055_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}

function campoProcuraProdutoAssociarItem(divId, D009_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/imp/imp003/form_func-ajax/campoProcuraProdutoAssociarItem/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Associar_Pesquisa').val(request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #D001_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #D009_Id').val(request.data['D009_Id']);			
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function desvincularProduto(T055_Id,divId){
		var buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/imp/imp003/grid_func-ajax/desvincularProduto/?ajax=true&T055_Id=' + encodeURIComponent(T055_Id) + '&callback=?', function(request) {
					if (request.code) {
						divRefresh(divId);
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    		dialogConfirm('Ok', request.data, buttons);    		
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    		dialogConfirm('Erro', request.data, buttons);
					}
					$('#' + divId).hideLoading();
					reloadUI(divId);
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Desvincular', 'Desvincular produto?', buttons);
	}

function excluirDI(T075_Id,divId){
	var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/imp/imp003/grid_func-ajax/excluirDI/?ajax=true&T075_Id=' + encodeURIComponent(T075_Id) + '&callback=?', function(request) {
				if (request.code) {
					divRefresh(divId);
					var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Ok', request.data, buttons);    		
				} else {
					var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Erro', request.data, buttons);
				}
				$('#' + divId).hideLoading();
				reloadUI(divId);
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Excluir DI', 'Confirma a exclusão da DI?', buttons);
}
 
} </script></div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function EST004ReverterCancelamento(T013_Id, divId){
	var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est004/grid_func-ajax/est004ReverterCancelamento/?ajax=true&T013_Id=' + encodeURIComponent(T013_Id) + '&callback=?', function(request) {			
				if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Operação Ok', 'Processo concluído com sucesso!', $buttons);
				    divRefresh(divId, true);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Erro', request.data, $buttons);
				}
				$('#' + divId).hideLoading();
				reloadUI(divId);
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Atenção!!', 'Esta nota encontra-se <b>CANCELADA</b>, deseja reverter este processo?', buttons);
}

function EST004ExcluirNota(T013_Id, divId){
	var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est004/grid_func-ajax/excluirNota/?ajax=true&T013_Id=' + encodeURIComponent(T013_Id) + '&callback=?', function(request) {			
				if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Ok', 'Nota excluída com sucesso!', $buttons);
				    divRefresh(divId, true);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Erro', request.data, $buttons);
				}
				$('#' + divId).hideLoading();
				reloadUI(divId);
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Atenção!!', 'Confirma a exclusão desta nota?', buttons);
}

function est005CampoCancelar(T084_Id, divId){
	$('#' + divId).showLoading();
		$.getJSON('/est/est005/grid_func-ajax/CampoCancelar/?ajax=true&T084_Id=' + encodeURIComponent(T084_Id) + '&callback=?', function(request) {
			// Aqui a variavél 'request' contém a resposta do AJAX
			if (request.code) {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm('Operação OK!', 'Operação realizada com sucesso.', buttons);
			} else {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm('Operação com ERRO!', request.data, buttons);
			}
			$('#' + divId).hideLoading();
		});
}

function est005CampoExcluir(T084_Id, divId){
	$('#' + divId).showLoading();
		$.getJSON('/est/est005/grid_func-ajax/CampoExcluir/?ajax=true&T084_Id=' + encodeURIComponent(T084_Id) + '&callback=?', function(request) {
			// Aqui a variavél 'request' contém a resposta do AJAX
			if (request.code) {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm('Operação OK!', 'Operação realizada com sucesso.', buttons);
				divRefresh(divId, true);
			} else {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm('Operação com ERRO!', request.data, buttons);
			}
			$('#' + divId).hideLoading();
		});
}

function est015CampoExclusao(T218_Id, divId) {
	var $buttons = {
		"Deletar": function() {
  			$('#' + divId).showLoading();
  			$.getJSON('/est/est015/grid_func-ajax/excluir/?ajax=true&T218_Id=' + encodeURIComponent(T218_Id) + '&callback=?', function(request) {
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

function est015CheckboxComprar(checkbox,T219_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est015/grid_func-ajax/est015CheckboxComprar/?ajax=true&T219_Id=' + encodeURIComponent(T219_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}

function est009CheckboxCotar(checkbox,D009_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est009/grid_func-ajax/est009CheckboxCotar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}

function est009CheckboxComprar(checkbox,D009_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est009/grid_func-ajax/est009CheckboxComprar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}

} </script></div>
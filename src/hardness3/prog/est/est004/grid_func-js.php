<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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

function est004ReceberNota(T007_Id, T007_Numero_Nota_Fiscal, divId, divIdRoot){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est004/grid_func-ajax/receberNota/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&callback=?', function(request) {
				if (request.code) {
					var $buttons = {
						"Fechar": function() { 
							$(this).dialog("destroy").remove(); 
							abrirJanela(false, divIdRoot, divId, unique(), '', 'Editar Nota Fiscal: ' + T007_Numero_Nota_Fiscal, '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + encodeURIComponent(request.T013_Id), [990,550]);
							divRefresh(divId, true);
						} 
					}
				    dialogConfirm('Ok', 'Nota de entrada cadastrada com sucesso!<br />Favor conferir e finalizar a nota', $buttons);
				    divRefresh(divId, true);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Erro', request.data, $buttons);
				}
				$('#' + divId).hideLoading();
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Confirma Entrada?', 'Confirma entrada da nota '+T007_Numero_Nota_Fiscal+'?', $buttons);
}

function est004editarT086_Quantidade(T012_Id, T013_Id){
	alert(T012_Id);
}

function T225_Gravar_Totalizacao(idChavePrimaria, campoEditado, valorAntigo, novoValor) {
	$.getJSON('/est/est004/grid_func-ajax/T225_Gravar_Totalizacao/?ajax=true&T225_Id=' + encodeURIComponent(idChavePrimaria) + '&callback=?', function(request) {
		divRefresh(gridPegarDivId($('.grid:visible:last')), true, false, false, idChavePrimaria.replace(/T225_Id-/, ''), 'T225_Id'); // grid
		//divRefresh(gridPegarDivId($('.grid:visible:last')), true);
	});
}

function duplicarItemNFEntrada(T014_Id, divId) {
	$('#' + divId).showLoading();
	$.getJSON('/est/est004/grid_func-ajax/duplicarItemNFEntrada/?ajax=true&T014_Id=' + encodeURIComponent(T014_Id) + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
		if (request.code) {
			divRefresh(divId, true, false, false, request.data, 'T014_Id'); // grid
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
		    dialogConfirm('Erro', request.data, $buttons);
		}
	});
}

function insereRegistroT001(T014_Id,divId){
		var buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/est/est004/grid_func-ajax/insereRegistroT001/?ajax=true&T014_Id=' + encodeURIComponent(T014_Id) + '&callback=?', function(request) {
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
		dialogConfirm('Inserir T001', 'Confirma a inclusão do registro no T001?', buttons);
}

function est004IncluirProduto(T014_Id, divId, divIdRoot){	
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est004/grid_func-ajax/est004IncluirProduto/?ajax=true&T014_Id=' + encodeURIComponent(T014_Id) + '&callback=?', function(request) {
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Ok', request.data, $buttons);					
					divRefresh(divId, true)
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Erro', request.data, $buttons);
				}
				$('#' + divId).hideLoading();				
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Atenção', 'Deseja incluir o produto? ', $buttons);
}

function est004ExcluirProduto(T014_Id, T014_T013_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/est/est004/grid_func-ajax/excluirProduto/?ajax=true&T014_Id=' + encodeURIComponent(T014_Id) + '&T014_T013_Id=' + encodeURIComponent(T014_T013_Id) + '&callback=?', function(request) {
				if (request.code) {
					divRefresh(divId, true);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					dialogConfirm('Erro', request.data, $buttons);
				}
				$('#' + divId).hideLoading();
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	dialogConfirm('Deseja Remover?', 'Você deseja realmente excluir este item?', $buttons);
}

} </script></div>
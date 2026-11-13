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
} </script></div>
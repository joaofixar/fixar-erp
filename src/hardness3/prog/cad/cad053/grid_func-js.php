<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad052ExcluirItem(D074_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad052/grid_func-ajax/excluirItem/?ajax=true&D074_Id=' + encodeURIComponent(D074_Id) + '&callback=?', function(request) {
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    //dialogConfirm('Ok', 'Item Removido', $buttons);
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

function cad052ExcluirProcesso(D075_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad052/grid_func-ajax/excluirProcesso/?ajax=true&D075_Id=' + encodeURIComponent(D075_Id) + '&callback=?', function(request) {
				if (request.code) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    //dialogConfirm('Ok', 'Item Removido', $buttons);
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
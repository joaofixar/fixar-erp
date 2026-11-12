<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function crm005ExcluirProduto(T099_Id, divId){
	var $buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/crm/crm005/grid_func-ajax/excluirProduto/?ajax=true&T099_Id=' + encodeURIComponent(T099_Id) + '&callback=?', function(request) {
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

function crm005Programacao(divId, data, D024_Id){
	var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/crm/crm005/grid_func-ajax/preencherPedido/?ajax=true&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&data=' +  + T099_Data + '&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$r_divIdParent}', true);
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    		dialogConfirm('Erro', request.data, buttons);
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
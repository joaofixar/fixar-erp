function cancelarProdutoOC(T225_Id, T225_Flag_Cancelada, divId){
	$.getJSON('/est/est016/grid_func-ajax/verificaExcluirProduto/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&callback=?', function(request) {
		if (request.code) {
			var buttons = {
				"Sim": function() {
					$('#' + divId).showLoading();
					$.getJSON('/est/est016/grid_func-ajax/cancelarProdutoOC/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&callback=?', function(request) {
						if (request.code) {
							divRefresh(divId);
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Ok', "Produto cancelado com sucesso.", buttons);    		
						} else {
							divRefresh(divId);
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
			dialogConfirm('Deseja cancelar?', 'Você deseja realmente cancelar este item?', buttons);
		} else {
			var buttons = {
				"Sim": function() {
					$('#' + divId).showLoading();
					$.getJSON('/est/est016/grid_func-ajax/cancelarProdutoOC/?ajax=true&T225_Id=' + encodeURIComponent(T225_Id) + '&callback=?', function(request) {
						if (request.code) {
							divRefresh(divId);
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Ok', "Produto cancelado com sucesso.", buttons);    		
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', "Não foi possível cancelar o produto.", buttons);
						}
						$('#' + divId).hideLoading();
						reloadUI(divId);
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
			dialogConfirm('Deseja cancelar?', 'Você deseja realmente cancelar este item?<br>' + request.data, buttons);
		}
	});
}
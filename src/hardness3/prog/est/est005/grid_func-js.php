<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
} </script></div>
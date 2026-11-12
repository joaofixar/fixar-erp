<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

 function est012AprovarCotacao(T173_Id, divId){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/est/est012/grid_func-ajax/est012AprovarCotacao/?ajax=true&T173_Id=' + encodeURIComponent(T173_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Ok', 'Cotação aprovada!', $buttons);
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
		dialogConfirm('Atenção!!', 'Deseja aprovar a cotação '+T173_Id+' ?', $buttons);
	}
} </script></div>
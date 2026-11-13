<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

 function ven005AprovarOrcamento(T003_Id, divId){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/ven/ven005/grid_func-ajax/ven005AprovarOrcamento/?ajax=true&T003_Id=' + encodeURIComponent(T003_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Ok', 'Orçamento aprovado!', $buttons);
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
		dialogConfirm('Atenção!!', 'Deseja aprovar o orçamento '+T003_Id+' ?', $buttons);
	}
} </script></div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

 function ven013AprovarPedido(T005_Id, divId){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/ven/ven013/grid_func-ajax/ven013AprovarPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {

					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Análise de pedido', request.data, $buttons);
				    divRefresh(divId);

					$('#' + divId).hideLoading();					
				});
				$(this).dialog("destroy").remove();
			},
			"Não": function() { $(this).dialog("destroy").remove(); } 
		};
		dialogConfirm('Atenção!!', 'Deseja aprovar o pedido '+T005_Id+' ?', $buttons);
	}
} </script></div>
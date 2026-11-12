<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function est003RetornarPedido(T005_Id, divId){
		var $buttons = {
			"Sim": function() {
				$('#' + divId).showLoading();
				$.getJSON('/est/est003/grid_func-ajax/retornarPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
					if (request.code) {
						var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    dialogConfirm('Ok', 'Pedido retornado!', $buttons);
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
		dialogConfirm('Atenção!!', 'Confirma retorno do pedido '+T005_Id+' para o vendedor?', $buttons);
	}
	
	function est003FaturarPedido(T005_Id, divId){
		$('#' + divId).showLoading();
		$.getJSON('/est/est003/grid_func-ajax/est003FaturarPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
			if (request.code) {
				dialogConfirm('Ok', request.data);
				divRefresh((divId), true);
			} else {
				dialogConfirm('Erro', request.data);
			}
		});	
	}

	function est003CheckboxBalanco(checkbox, T066_Id, divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();                
	$.getJSON('/est/est003/grid_func-ajax/est002CheckboxBalanco/?ajax=true&T066_Id=' + encodeURIComponent(T066_Id) + '&selected=' + selected + '&callback=?', function(request) {
		if(!request.code){
			dialogConfirm('Erro', request.data);
			$(checkbox).attr('checked', false);
		}
		$('#' + divId).hideLoading();
	});    
}

	function est003CheckboxGerarPedido(checkbox, T206_Id, divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
		$('#' + divId).showLoading();                
		$.getJSON('/est/est003/grid_func-ajax/est003CheckboxGerarPedido/?ajax=true&T206_Id=' + encodeURIComponent(T206_Id) + '&selected=' + selected + '&callback=?', function(request) {
			if(!request.code){
				dialogConfirm('Erro', request.data);
				$(checkbox).attr('checked', false);
			}
			$('#' + divId).hideLoading();
		});    
	}
} </script></div>
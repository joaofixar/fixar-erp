function gravarDesconto(elem, divId, T003_Id, valor) {   
	//if (!isempty($('#T003_Valor_Desconto').val()) && $('#T003_Valor_Desconto').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/ven/ven001/form_func-ajax/gravarDesconto/?ajax=true&T003_Valor_Desconto=' + encodeURIComponent($('#T003_Valor_Desconto').val()) + '&T003_Valor_Desconto_Confirmado=' + encodeURIComponent($('#T003_Valor_Desconto_Confirmado').val()) + '&T003_Id=' + encodeURIComponent(T003_Id) + '&Valor=' + encodeURIComponent(valor) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					// $('#T003_D006_Id').html(request.data['T003_D006_Id']);
					// $('#T003_D013_Id').html(request.data['T003_D013_Id']); 
					// $('#T003_Flag_Revenda_Consumidor').val(request.data['T003_Flag_Revenda_Consumidor']);
					// $('#T003_C007_Id_Vendedor_Interno').val(request.data['T003_C007_Id_Vendedor_Interno']);
					divRefresh(divId);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}, 100);
	//}
} 
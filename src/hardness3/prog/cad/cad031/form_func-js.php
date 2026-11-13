<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function alteraEstadoEmpresas(elem, divId) {
	    $('#' + divId).showLoading();
	    $.getJSON('/cad/cad031/form_func-ajax/alteraEstadoEmpresas/?ajax=true&C004_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#C004_D020_Id').html(request.data['C004_D020_Id']);
				reloadUI('#' . divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
	    		dialogConfirm("Erro!", request.data, $buttons)
			}
		});
	}

	function alteraSeguradora(elem, divId) {
	    $('#' + divId).showLoading();
	    $.getJSON('/cad/cad031/form_func-ajax/carregaSeguradoraApolices/?ajax=true&C004_D172_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#C004_D173_Id').html(request.data['C004_D173_Id']);
				reloadUI('#' . divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
	    		dialogConfirm("Erro!", request.data, $buttons)
			}
		});
	}
} </script></div>
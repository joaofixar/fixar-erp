<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function alteraEstado(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad001/form_func-ajax/alteraEstado/?ajax=true&D024_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D024_D020_Id').html(request.data['D024_D020_Id']);
			//$('#D148_D020_Id').html(request.data['D024_D020_Id']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
function alteraEstadoCadastroEndereco(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad001/form_func-ajax/alteraEstado/?ajax=true&D024_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D148_D020_Id').html(request.data['D024_D020_Id']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
function alteraEstadoCobranca(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad001/form_func-ajax/alteraEstadoCobranca/?ajax=true&D024_D018_Cobranca=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D024_D020_Cobranca').html(request.data['D024_D020_Cobranca']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

function alteraEstadoEntrega(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad001/form_func-ajax/alteraEstadoEntrega/?ajax=true&D024_D018_Entrega=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D024_D020_Entrega').html(request.data['D024_D020_Entrega']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

function campoNomeFantasia(elem, divId) {
	if ($('#' + divId + ' #D024_Nome_Fantasia').val() == '') {
		$('#' + divId + ' #D024_Nome_Fantasia').val($(elem).val());
	}
}


} </script></div>
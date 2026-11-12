<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

/**
* Utilizado exclussivamente no formulário 'Contas a Pagar' -> 'Reparcelar'
* Faz uma cópia do primeiro campo (linha) e modifica os seus dados
*/

function alteraConta(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad058/form_func-ajax/alteraConta/?ajax=true&D032_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#contaSTContasPagar').html(request.data['contaSTContasPagar']);
			reloadUI('#' . divId);
		} else {
			$('#contaSTContasPagar').html(0);
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

} </script></div>
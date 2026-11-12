<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function alteraEstadoTransportadoras(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad004/form_func-ajax/alteraEstadoTransportadoras/?ajax=true&D022_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D022_D020_Id').html(request.data['D022_D020_Id']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
} </script></div>
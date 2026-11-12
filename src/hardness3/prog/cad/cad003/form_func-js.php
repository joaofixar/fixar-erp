<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function alteraEstadoRepresentantes(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad003/form_func-ajax/alteraEstadoRepresentantes/?ajax=true&D021_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D021_D020_Id').html(request.data['D021_D020_Id']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
} </script></div>
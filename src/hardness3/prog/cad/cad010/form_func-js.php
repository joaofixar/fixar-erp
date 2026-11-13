<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function alteraEstado(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad010/form_func-ajax/alteraEstado/?ajax=true&D109_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D109_D020_Id').html(request.data['D109_D020_Id']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
} </script></div>
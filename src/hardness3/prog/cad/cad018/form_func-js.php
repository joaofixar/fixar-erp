<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function alteraEstado(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/cad/cad018/form_func-ajax/alteraEstado/?ajax=true&D119_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#D119_D020_Id').html(request.data['D119_D020_Id']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
} </script></div>
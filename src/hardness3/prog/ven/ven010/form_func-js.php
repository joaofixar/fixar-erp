<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function carregaVeicEmb(elem, divId) {
    $('#' + divId).showLoading();
    setTimeout(function() {
	    $.getJSON('/ven/ven010/form_func-ajax/carregaVeicEmb/?ajax=true&T141_D024_Id=' + encodeURIComponent($('#T141_D024_Id').val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#T141_D116_Id').html(request.data['T141_D116_Id']);
				reloadUI('#' . divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    			dialogConfirm("Erro!", request.data, $buttons)
			}
		});
	}, 100);
}
} </script></div>
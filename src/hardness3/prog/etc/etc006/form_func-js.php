<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function alteraEstado(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/ven/ven009/form_func-ajax/alteraEstado/?ajax=true&T143_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T143_D020_Id').html(request.data['T143_D020_Id']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

function carregaContatos(divId) {
    $('#' + divId).showLoading();
    $.getJSON('/etc/etc006/form_func-ajax/carregaContatos/?ajax=true&D024_Id=' + encodeURIComponent($('#T258_D024_Id').val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T258_D013_Id').html(request.data['D013_Id']);
			var T258_D013_Id_refreshBtn = $('#T258_D013_Id_refreshBtn').attr('onclick');
			var matches = T258_D013_Id_refreshBtn.match(/, '[^']+'/g);
			T258_D013_Id_refreshBtn = T258_D013_Id_refreshBtn.replace(matches[1], ", '" + request.data['D013_Id_refreshBtn'] + "'");
			$('#T258_D013_Id_refreshBtn').attr('onclick', T258_D013_Id_refreshBtn);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
} </script></div>
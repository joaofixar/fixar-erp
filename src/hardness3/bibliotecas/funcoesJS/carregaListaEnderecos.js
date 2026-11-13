function carregaListaEnderecos(elem, divId) {
	if (!isempty($('#T268_D024_Id').val()) && $('#T268_D024_Id').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/est/est002/form_func-ajax/carregaListaEnderecos/?ajax=true&T268_D024_Id=' + encodeURIComponent($('#T268_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					if ($('#T268_D148_Id').size() > 0) { $('#T268_D148_Id').html(request.data['T268_D148_Id']); }
					var T268_D148_Id_refreshBtn = $('#T268_D148_Id_refreshBtn').attr('onclick');
					var matches = T268_D148_Id_refreshBtn.match(/, '[^']+'/g);
					T268_D148_Id_refreshBtn = T268_D148_Id_refreshBtn.replace(matches[1], ", '" + request.data['T268_D148_Id_refreshBtn'] + "'");
					$('#T268_D148_Id_refreshBtn').attr('onclick', T268_D148_Id_refreshBtn);

					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data['erro'], $buttons)
				}
			});
		}, 100);
	}
}
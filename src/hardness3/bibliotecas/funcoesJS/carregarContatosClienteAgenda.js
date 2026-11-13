function carregarContatosClienteAgenda(elem, divId) {
	$('#' + divId).showLoading();
	setTimeout(function() {
		$.getJSON('/crm/crm001/form_func-ajax/carregarContatosClienteAgenda/?ajax=true&D024_Id=' + encodeURIComponent($('#D085_D024_Id').val()) + '&callback=?', function(request) {
		    $('#' + divId).hideLoading();
			if (request.code) {
				if ($('#D085_D013_Id').size() > 0) { $('#D085_D013_Id').html(request.data['D085_D013_Id']); }
				var D085_D013_Id_refreshBtn = $('#D085_D013_Id_refreshBtn').attr('onclick');
				var matches = D085_D013_Id_refreshBtn.match(/, '[^']+'/g);
				D085_D013_Id_refreshBtn = D085_D013_Id_refreshBtn.replace(matches[1], ", '" + request.data['D085_D013_Id_refreshBtn'] + "'");
				$('#D085_D013_Id_refreshBtn').attr('onclick', D085_D013_Id_refreshBtn);

				if ($('#D085_D024_Id_Pesquisa').data('definirContato') > 0) {
					$('.itemForm-D085_D013_Id').val($('#D085_D024_Id_Pesquisa').data('definirContato'));
				}
            } else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['erro'], $buttons)
			}
		});
	}, 100);
}
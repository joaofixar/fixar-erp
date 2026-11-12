function separacaoProdutoPorLocacao(elem, divId) {
	$('#' + divId).showLoading();
    setTimeout(function() {
	    $.getJSON('/est/est002/form_func-ajax/carregarLocacoes/?ajax=true&T006_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				// Locações
				if ($('#T238_T066_Id').size() > 0) { $('#T238_T066_Id').html(request.data['T238_T066_Id']); } 
				var T238_T066_Id_refreshBtn = $('#T238_T066_Id_refreshBtn').attr('onclick');
				var matches = T238_T066_Id_refreshBtn.match(/, '[^']+'/g);
				T238_T066_Id_refreshBtn = T238_T066_Id_refreshBtn.replace(matches[1], ", '" + request.data['T238_T066_Id_refreshBtn'] + "'");
				$('#T238_T066_Id_refreshBtn').attr('onclick', T238_T066_Id_refreshBtn);
				$('#alterarLocacao').attr('D009_Id', request.data['T006_D009_Id']);
				reloadUI('#' + divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['erro'], $buttons)
			}
		});
	}, 100);
}
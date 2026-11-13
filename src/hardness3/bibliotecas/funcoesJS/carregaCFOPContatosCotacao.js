function carregaCFOPContatosCotacao(elem, divId) {
	if (!isempty($('#T218_D024_Id').val()) && $('#T218_D024_Id').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/est/est015/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T218_D024_Id=' + encodeURIComponent($('#T218_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#T218_D006_Id').html(request.data['T218_D006_Id']);
					if ($('#T218_D013_Id').size() > 0) { $('#T218_D013_Id').html(request.data['T218_D013_Id']); } 
                    if ($('#T218_D021_Id').size() > 0) { $('#T218_D021_Id').html(request.data['T218_D021_Id']); } 
					if ($('#T218_D116_Id').size() > 0) { $('#T218_D116_Id').html(request.data['T218_D116_Id']); }
					var T218_D013_Id_refreshBtn = $('#T218_D013_Id_refreshBtn').attr('onclick');
					var matches = T218_D013_Id_refreshBtn.match(/, '[^']+'/g);
					T218_D013_Id_refreshBtn = T218_D013_Id_refreshBtn.replace(matches[1], ", '" + request.data['T218_D013_Id_refreshBtn'] + "'");
					$('#T218_D013_Id_refreshBtn').attr('onclick', T218_D013_Id_refreshBtn);
					
					$('#T218_Flag_Revenda_Consumidor').val(request.data['T218_Flag_Revenda_Consumidor']);
					$('#T218_C007_Id_Vendedor_Interno').val(request.data['T218_C007_Id_Vendedor_Interno']);
					$('#T218_Percentual_Comissao').val(request.data['T218_Percentual_Comissao']);
					$('#T218_Flag_Expedicao').html(request.data['T218_Flag_Expedicao']);
					$('#T218_Nome_Cliente').val(request.data['T218_Nome_Cliente']);

					if ($('#T218_D024_Id_Pesquisa').data('definirContato') > 0) {
						$('.itemForm-T218_D013_Id').val($('#T218_D024_Id_Pesquisa').data('definirContato'));
					}

					var T218_D116_Id_refreshBtn = $('#T218_D116_Id_refreshBtn').attr('onclick');
					var matches = T218_D116_Id_refreshBtn.match(/, '[^']+'/g);
					T218_D116_Id_refreshBtn = T218_D116_Id_refreshBtn.replace(matches[1], ", '" + request.data['T218_D116_Id_refreshBtn'] + "'"); 
					$('#T218_D116_Id_refreshBtn').attr('onclick', T218_D116_Id_refreshBtn); 
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data['erro'], $buttons)
				}
			});
		}, 100);
	}
}
function carregaRecebedor(elem, divId) {    
	if (!isempty($('#T084_D024_Destinatario_Id').val()) && $('#T084_D024_Destinatario_Id').val() != 0) {
	    setTimeout(function() {
            $.getJSON('/est/est020/form_func-ajax/carrega_Recebedor/?ajax=true&T084_D024_Destinatario_Id=' + encodeURIComponent($('#T084_D024_Destinatario_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					if ($('#T084A_D024_Recebedor_Id').size() > 0) { $('#T084A_D024_Recebedor_Id').html(request.data['T084A_D024_Recebedor_Id']); } 

					var T084A_D024_Recebedor_Id_refreshBtn = $('#T084A_D024_Recebedor_Id_refreshBtn').attr('onclick');
					var matches = T084A_D024_Recebedor_Id_refreshBtn.match(/, '[^']+'/g);
					T084A_D024_Recebedor_Id_refreshBtn = T084A_D024_Recebedor_Id_refreshBtn.replace(matches[1], ", '" + request.data['T084A_D024_Recebedor_Id_refreshBtn'] + "'");
					$('#T084A_D024_Recebedor_Id_refreshBtn').attr('onclick', T084A_D024_Recebedor_Id_refreshBtn);
					
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data['erro'], $buttons)
				}
			});
		}, 100);
	}
}
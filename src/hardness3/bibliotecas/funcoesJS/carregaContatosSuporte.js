function carregaContatosSuporte(elem, divId) {
	if (!isempty($('#T234_D024_Id').val()) && $('#T234_D024_Id').val() != 0) {
		$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/outros/registroSuporte/registroSuporte/carregaContatosSuporte/?ajax=true&T234_Id=' + encodeURIComponent($('#T234_Id').val()) + '&T234_D024_Id=' + encodeURIComponent($('#T234_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					if ($('#T234_D013_Id').size() > 0) { 
						$('#T234_D013_Id').html(request.data['T234_D013_Id']);  
					} 
					$('#T234_D013_Id').html(request.data['T234_D013_Id']);
					$('#T234_Valor_Hora_Tecnica').val(request.data['T234_Valor_Hora_Tecnica']);
					
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data['erro'], $buttons)
				}
			});
		}, 100);
	}
}
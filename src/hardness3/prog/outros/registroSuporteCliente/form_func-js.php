<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

/**
* Utilizado exclusivamente no formulário 'Lista' -> 
*/

function alteraLista(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/outros/registroSuporte/registroSuporte/alteraLista/?ajax=true&D166_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T234_D167_Id').html(request.data['T234_D167_Id']);
			reloadUI('#' . divId);
		} else {
			alert("Erro");
			$('#T234_D167_Id').html(0);
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

function enviarRespostaHardness(T252_Id,T234_Id,dest,divId) {
	var destinatario;
	var buttons = {
		"Sim": function() {
			$.getJSON('/outros/registroSuporte/registroSuporte/enviarRespostaHardnessParceiro/?ajax=true&T252_Id=' + encodeURIComponent(T252_Id) + '&destinatario=' + encodeURIComponent(dest) + '&T234_Id=' + encodeURIComponent(T234_Id) + '&callback=?', function(request) {
				if (request.code) {
					var buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Ok', request.data, buttons2);
				    divRefresh(divId);
				} else {
					var buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    dialogConfirm('Erro', request.data, buttons2);
				}
			});
			$(this).dialog("destroy").remove();
		},
		"Não": function() { $(this).dialog("destroy").remove(); } 
	};
	if(dest=='H'){
		destinatario='cliente';
	}else{
		destinatario='Hardness';
	}
	dialogConfirm('Envio', 'Confirma envio da resposta para '+destinatario+'?', buttons);
}

} </script></div>
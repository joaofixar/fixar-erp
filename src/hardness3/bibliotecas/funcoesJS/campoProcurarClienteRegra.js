function campoProcurarClienteRegra(divId, D024_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/fin/fin003/form_func-ajax/atualizarCamposCliente/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			console.log(request.data);
			$('#' + divId + ' #T217_D024_Id').val(request.data['D024_Id']);
			$('#' + divId + ' #pesquisaGridCliente_Pesquisa').val(request.data['D024_Nome_Empresa']);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
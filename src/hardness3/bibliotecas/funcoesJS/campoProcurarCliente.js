function campoProcurarCliente(divId, D024_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposCliente/?ajax=true&D024_Id=' + encodeURIComponent(D024_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId + ' #T003_D024_Id').val(request.data['D024_Id']);
			$('#' + divId + ' #T003_D024_Id_Pesquisa').val(request.data['D024_Nome_Empresa']);
			$('#' + divId + ' #T003_C007_Id_Vendedor_Interno').val(request.data['D024_C007_Vendedor_Interno']);
			// Focus
			$('#' + divId + ' #T003_D013_Id').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
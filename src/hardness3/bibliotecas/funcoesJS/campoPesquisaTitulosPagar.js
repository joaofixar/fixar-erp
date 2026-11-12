function campoPesquisaTitulosPagar(divId, T015_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/fin/fin002/form_func-ajax/pesquisaTitulosPagar/?ajax=true&T015_Id=' + encodeURIComponent(T015_Id) + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
		if (request.code){
			$('#' + divId + ' #Pesquisa_Conta_Pesquisa').val(request.data['T015_Nome_Cliente']);
			$('#' + divId + ' #T015_Nome_Cliente').val(request.data['T015_Nome_Cliente']);
			$('#' + divId + ' #T015_D024_Id').val(request.data['T015_D024_Id']);
			$('#' + divId + ' #T015_D024_Id_Pesquisa').val(request.data['D024_Nome_Empresa']);
			$('#' + divId + ' #T015_D014_Id').val(request.data['T015_D014_Id']);
			$('#' + divId + ' #T015_D032_Id').val(request.data['T015_D032_Id']);
			$('#' + divId + ' #T015_D014_Id').val(request.data['T015_D014_Id']);
			$('#' + divId + ' #T015_D073_Id').val(request.data['T015_D073_Id']);
			$('#' + divId + ' #T015_Data_Emissao').val(request.data['T015_Data_Emissao']);
			if(request.data['T015_Data_Vencimento']){
				$('#' + divId + ' #T015_Data_Vencimento').val(request.data['T015_Data_Vencimento']);
			}
			$('#' + divId + ' #T015_Numero_Documento').val('');
			$('#' + divId + ' #T015_Numero_Duplicata').val('');
			$('#' + divId + ' #T015_D031_Id').val(request.data['T015_D031_Id']);
			$('#' + divId + ' #T015_Valor_Duplicata').val(request.data['T015_Valor_Duplicata']);
			$('#' + divId + ' #T015_Observacao').val(request.data['T015_Observacao']);
			// Focus
			$('#' + divId + ' #T015_Codigo_Barras').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			dialogConfirm('Erro', request.data, $buttons);
		}
	});
}
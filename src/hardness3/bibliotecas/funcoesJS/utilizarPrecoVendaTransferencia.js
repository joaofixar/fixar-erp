function utilizarPrecoVendaTransferencia(divId, divIdParent, idItemVenda, tabela,  C004_Id_Transferencia) {
	var buttons = {
		"Sim": function() {
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad002/grid_func-ajax/atualizarPrecoVendaTransferencia/?ajax=true&idItemVenda=' + encodeURIComponent(idItemVenda) + '&tabela=' + encodeURIComponent(tabela) + '&C004_Id_Transferencia=' + encodeURIComponent(C004_Id_Transferencia) + '&callback=?', function(request) {
				$('#' + divId).hideLoading();
				if (request.code) {
					dialogConfirm('Ok', 'Preço tabela atualizado com sucesso!');
					divRefresh(divId, true, false, false, idItemVenda, tabela+'_Id');
					divRefresh(divIdParent, true, false, false, idItemVenda, tabela+'_Id');
				} else {
					dialogConfirm('Erro', request.data);
				}
			});
			$(this).dialog("destroy").remove();
	 	},
		"Não": function() { $(this).dialog("destroy").remove(); }
	};
	dialogConfirm('Atualizar preço tabela', 'Deseja atualizar o preço tabela deste produto com o preço de transferência?', buttons);
}
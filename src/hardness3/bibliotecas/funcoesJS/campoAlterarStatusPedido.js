function campoAlterarStatusPedido(T005_Id, divId) {
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven002/form_func-ajax/carregarStatusPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
		$('#' + divId).hideLoading();

		var $buttons = {
			"Alterar": function() {
				var statusSelecionado = $(this).find('select').val();
				if(statusSelecionado == "undefined"){
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    	dialogConfirm('Erro', "Status selecionado é invalido!", $buttons);
				} else {					
					var $buttons2 = {
						"Sim": function() {
							$('#' + divId).showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/alterarStatusPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&status=' + encodeURIComponent(statusSelecionado) + '&callback=?', function(request) {
								$('#' + divId).hideLoading();
								if (request.code) {
									var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    			dialogConfirm('Ok', "O status do pedido foi alterado com sucesso!", $buttons);
								} else {
									var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    			dialogConfirm('Erro', request.data, $buttons);
								}
								divRefresh(divId);
							});
							$(this).dialog("destroy").remove();
							$('#dc-' + novoId).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove();} 
					};
					dialogConfirm('Atenção', 'Confirma alteração do status do pedido?', $buttons2);
				}
			},
			"Cancelar": function() { $(this).dialog("destroy").remove();} 
		};
		var novoId = dialogConfirm('Alterar status do pedido', 'Selecione o status desejado:', $buttons);
		$('<select>').attr('id', 'comboStatusPedido').addClass('formInput ui-state-default ui-corner-all').css('width', '250px').appendTo($("#dc-" + novoId)).focus();
		$('#comboStatusPedido').append("<option value='undefined'>...</option>");
		$.each(request.data, function(status, nome){
			$('#comboStatusPedido').append("<option value='"+ status +"'>" + nome + "</option>");
		});
		return false;
	});
}
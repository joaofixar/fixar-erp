<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	/**
	 * Abre o seletor de etapas permitidas e envia o retorno pela rota compartilhada da expedição.
	 */
	function est002RetornarPedidoExpedicao(T005_Id, divId, T005_Flag_Status){
		// Mapeia a ordem lógica das etapas para liberar apenas estágios anteriores ao atual.
		var etapas = [
			{ value: '1', label: 'Impressão' },
			{ value: '2', label: 'Separação' },
			{ value: '10', label: 'Conferência' },
			{ value: '3', label: 'Gerar nota fiscal' },
			{ value: '4', label: 'Aguardando coleta' }
		];
		var ordem = {
			'1': 0,
			'2': 1,
			'10': 2,
			'3': 3,
			'4': 4,
			'5': 5
		};
		var etapaAtual = String(T005_Flag_Status || '');
		var indiceAtual = typeof(ordem[etapaAtual]) != 'undefined' ? ordem[etapaAtual] : -1;
		var opcoes = [];

		// Filtra apenas as etapas anteriores para evitar avanço ou retorno para o mesmo estágio.
		for (var i = 0; i < etapas.length; i++) {
			if (typeof(ordem[etapas[i].value]) != 'undefined' && ordem[etapas[i].value] < indiceAtual) {
				opcoes.push(etapas[i]);
			}
		}

		if (opcoes.length === 0) {
			dialogConfirm('Atenção', 'Não existem etapas anteriores disponíveis para o pedido ' + T005_Id + '.');
			return;
		}

		// Monta um formulário simples para o usuário escolher a etapa de retorno.
		var dialogId = 'est002RetornarPedidoExpedicao_' + unique();
		var html = '<div id="' + dialogId + '">' +
			'<div style="margin-bottom: 8px;">Selecione para qual etapa o pedido ' + T005_Id + ' deve retornar:</div>' +
			'<select id="' + dialogId + '_select" class="btnselect ui-corner-all" style="width: 100%; height: 24px;">';

		for (var j = 0; j < opcoes.length; j++) {
			html += '<option value="' + opcoes[j].value + '">' + opcoes[j].label + '</option>';
		}

		html += '</select></div>';

		var $buttons = {
			"Retornar": function() {
				var etapaDestino = $('#' + dialogId + '_select').val();

				// A operação é centralizada no ajax da expedição para manter o mesmo comportamento em todos os grids.
				$('#' + divId).showLoading();
				$.getJSON('/est/est002/grid_func-ajax/retornarPedidoExpedicao/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&T005_Flag_Status=' + encodeURIComponent(etapaDestino) + '&callback=?', function(request) {
					if (request.code) {
						dialogConfirm('Ok', 'Pedido retornado com sucesso.');
						divRefresh(divId, true);
					} else {
						dialogConfirm('Erro', request.data);
					}
					$('#' + divId).hideLoading();
				});
				$(this).dialog("destroy").remove();
			},
			"Cancelar": function() { $(this).dialog("destroy").remove(); }
		};

		dialogConfirm('Retornar etapa', html, $buttons);
	}
} </script></div>

var idDialog = 0;
function carregaCFOPContatos(elem, divId, T003_C004_Id_Estoque, alterarVendedorAusente, alterarVendedor) {
	if (!isempty($('#T003_D024_Id').val()) && $('#T003_D024_Id').val() != 0) {
		var T003_D024_Id = $('#T003_D024_Id').val();
    	var setouCliente = 'N';

	    setTimeout(function() {
			if ($('#T003_D024_Id').val() === "" || $('#T003_D024_Id').val() == 0) {
				// Se estiver vazio, define novamente o valor desejado, são casos que da erro no restricao do T003.php e acabava perdendo o cliente
				$('#T003_D024_Id').val(T003_D024_Id);
				setouCliente = 'S';
			}
		    $.getJSON('/ven/ven001/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T003_D024_Id=' + encodeURIComponent($('#T003_D024_Id').val()) + '&T003_C004_Id_Estoque=' + encodeURIComponent(T003_C004_Id_Estoque) + '&T003_C007_Id_Vendedor_Interno=' + encodeURIComponent($('#T003_C007_Id_Vendedor_Interno').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					if(setouCliente == 'S'){
						$('#T003_D024_Id_Pesquisa').val(request.data['T003_D024_Id_Pesquisa']);
					}
					if (request.data['dialog'] && typeof request.data['dialog'] === "object") {
						var $buttons = {};
						if (request.data['dialog']['buttons'] && request.data['dialog']['buttons'].trim() !== "") {
							$buttons = eval("(" + request.data['dialog']['buttons'] + ")");
						}

						if (idDialog === 0) {
							if (Object.keys($buttons).length > 0) {
								idDialog = dialogConfirm(request.data['dialog']['titulo'], request.data['dialog']['msg'], $buttons);
							} else {
								idDialog = dialogConfirm(request.data['dialog']['titulo'], request.data['dialog']['msg']);
							}
						}
					}
					
					$('#T003_D006_Id').html(request.data['T003_D006_Id']);
					if ($('#T003_D013_Id').size() > 0) { $('#T003_D013_Id').html(request.data['T003_D013_Id']); } 
                    if ($('#T003_D021_Id').size() > 0) { $('#T003_D021_Id').html(request.data['T003_D021_Id']); } 
					if ($('#T003_D116_Id').size() > 0) { $('#T003_D116_Id').html(request.data['T003_D116_Id']); }
					var T003_D013_Id_refreshBtn = $('#T003_D013_Id_refreshBtn').attr('onclick');
					if (T003_D013_Id_refreshBtn) {
						var matches = T003_D013_Id_refreshBtn.match(/, '[^']+'/g);
						if (matches && matches[1]) {
							T003_D013_Id_refreshBtn = T003_D013_Id_refreshBtn.replace(matches[1], ", '" + request.data['T003_D013_Id_refreshBtn'] + "'");
							$('#T003_D013_Id_refreshBtn').attr('onclick', T003_D013_Id_refreshBtn);
						}
					}
					$('#T003_Flag_Revenda_Consumidor').val(request.data['T003_Flag_Revenda_Consumidor']);
					$('#T003_C007_Id_Vendedor_Interno').val(request.data['T003_C007_Id_Vendedor_Interno']);
					$('#T003_C007_Id_Vendedor_Externo').val(request.data['T003_C007_Id_Vendedor_Externo']);
					$('#T003_Percentual_Comissao').val(request.data['T003_Percentual_Comissao']);
					$('#T003_Percentual_Comissao_Representante').val(request.data['T003_Percentual_Comissao_Representante']);
					$('#T003_Percentual_Comissao_Externo').val(request.data['T003_Percentual_Comissao_Externo']);
					$('#T003_Percentual_Comissao_Usuario').val(request.data['T003_Percentual_Comissao_Usuario']);
					$('#T003_Flag_Expedicao').html(request.data['T003_Flag_Expedicao']);
					$('#T003_Nome_Cliente').val(request.data['T003_Nome_Cliente']);
					if(request.data['setaDestinoVenda']){
						$('#T003_Flag_Destino_Produto').val(request.data['T003_Flag_Destino_Produto']);
					}					
					$('#T003_D022_Id').val(request.data['T003_D022_Id']);
					$('#T003_D022_Id_Pesquisa').val(request.data['T003_D022_Id_Pesquisa']);

					if (request.data['Altera_Flag_Acp']) {
						$('#T003_Flag_Acp').val(request.data['T003_Flag_Acp']);
					}

					// endereco entrega
					if ($('#T003_D148_Id_Entrega').size() > 0) { $('#T003_D148_Id_Entrega').html(request.data['T003_D148_Id_Entrega']); } 
					var T003_D148_Id_Entrega_refreshBtn = $('#T003_D148_Id_Entrega_refreshBtn').attr('onclick');
					if (T003_D148_Id_Entrega_refreshBtn) {
						var matches = T003_D148_Id_Entrega_refreshBtn.match(/, '[^']+'/g);
						if (matches && matches[1]) {
							T003_D148_Id_Entrega_refreshBtn = T003_D148_Id_Entrega_refreshBtn.replace(matches[1], ", '" + request.data['T003_D148_Id_Entrega_refreshBtn'] + "'");
							$('#T003_D148_Id_Entrega_refreshBtn').attr('onclick', T003_D148_Id_Entrega_refreshBtn);
						}
					}

					// endereco cobranca
					if ($('#T003_D148_Id_Cobranca').size() > 0) { $('#T003_D148_Id_Cobranca').html(request.data['T003_D148_Id_Cobranca']); } 
					var T003_D148_Id_Cobranca_refreshBtn = $('#T003_D148_Id_Cobranca_refreshBtn').attr('onclick');
					if (T003_D148_Id_Cobranca_refreshBtn) {
						var matches = T003_D148_Id_Cobranca_refreshBtn.match(/, '[^']+'/g);
						if (matches && matches[1]) {
							T003_D148_Id_Cobranca_refreshBtn = T003_D148_Id_Cobranca_refreshBtn.replace(matches[1], ", '" + request.data['T003_D148_Id_Cobranca_refreshBtn'] + "'");
							$('#T003_D148_Id_Cobranca_refreshBtn').attr('onclick', T003_D148_Id_Cobranca_refreshBtn);
						}
					}

					// endereco redespacho
					if ($('#T003A_D148_Id_Redespacho').size() > 0) { $('#T003A_D148_Id_Redespacho').html(request.data['T003A_D148_Id_Redespacho']); } 
					var T003A_D148_Id_Redespacho_refreshBtn = $('#T003A_D148_Id_Redespacho_refreshBtn').attr('onclick');
					if (T003A_D148_Id_Redespacho_refreshBtn) {
						var matches = T003A_D148_Id_Redespacho_refreshBtn.match(/, '[^']+'/g);
						if (matches && matches[1]) {
							T003A_D148_Id_Redespacho_refreshBtn = T003A_D148_Id_Redespacho_refreshBtn.replace(matches[1], ", '" + request.data['T003A_D148_Id_Redespacho_refreshBtn'] + "'");
							$('#T003A_D148_Id_Redespacho_refreshBtn').attr('onclick', T003A_D148_Id_Redespacho_refreshBtn);
						}
					}

					// envio automatico do form
					if ($('#T003_D024_Id_Pesquisa').data('enviarFormAutomatico') == 1) {
						$('#T003_D013_Id').val($('#T003_D013_Id option:eq(1)').val());
						if (!isempty($('#T003_D024_Id_Pesquisa').closest('form').data('func'))) {
							window[$('#T003_D024_Id_Pesquisa').closest('form').data('func')]();
						}
					}
					if ($('#T003_D024_Id_Pesquisa').data('definirContato') > 0) {
						$('.itemForm-T003_D013_Id').val($('#T003_D024_Id_Pesquisa').data('definirContato'));
						$('.itemForm-T003_Cliente_Area').focus();
					}

					$('.itemForm-T003_C007_Id_Vendedor_Interno').attr('onfocus', 'this.initialSelect = this.selectedIndex;').attr('onchange', 'this.selectedIndex = this.initialSelect;').attr('readonly', 'readonly');
					if(request.data['notificarVendedor'] == 1){
						$('.itemForm-T003_C007_Id_Vendedor_Interno').css('border', '1px solid #023adf');
					} else if(request.data['notificarVendedor'] == 2){
						$('.itemForm-T003_C007_Id_Vendedor_Interno').css('border', '1px solid #E99601');
						if(alterarVendedorAusente == 'S'){
							$('.itemForm-T003_C007_Id_Vendedor_Interno').removeAttr('onfocus').removeAttr('onchange').removeAttr('readonly');
						}
					}
					if(alterarVendedor == 'S'){
						$('.itemForm-T003_C007_Id_Vendedor_Interno').removeAttr('onfocus').removeAttr('onchange').removeAttr('readonly');
					}
					if(request.data['T003_Flag_Frete']!==''){
						$('#T003_Flag_Frete').val(request.data['T003_Flag_Frete']);
					}
					
					var T003_D116_Id_refreshBtn = $('#T003_D116_Id_refreshBtn').attr('onclick');
					if (T003_D116_Id_refreshBtn) {
						var matches = T003_D116_Id_refreshBtn.match(/, '[^']+'/g);
						if (matches && matches[1]) {
							T003_D116_Id_refreshBtn = T003_D116_Id_refreshBtn.replace(matches[1], ", '" + request.data['T003_D116_Id_refreshBtn'] + "'");
							$('#T003_D116_Id_refreshBtn').attr('onclick', T003_D116_Id_refreshBtn);
						}
					}
					
					if (divId) {
						reloadUI(divId.charAt(0) === '#' ? divId : '#' + divId);
					}
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data['erro'], $buttons)
				}
			});
		}, 100);
	}
}

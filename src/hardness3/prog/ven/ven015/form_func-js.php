<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

	function campoProcuraProdutoPDV(divId,T007_Id,D009_Id) {
		//divSetarAcaoId(divId, D009_Id, 'D009_Id');
		$('#' + divId).showLoading();
		$.getJSON('/ven/ven015/form_func-ajax/atualizarCamposProdutosPDV/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
			if (request.code) {
				// Preenche os campos
				//$('#' + divId + ' #T008_Codigo_Substituto').val(request.data['T008_Codigo_Substituto']);
				$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
				$('#' + divId + ' #T008_D037_Id').val(request.data['D001_D037_Id']);
				$('#' + divId + ' #T008_D005_Id').val(request.data['D001_D005_Id']);
				$('#' + divId + ' #T008_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
				$('#' + divId + ' #T008_D009_Id').val(request.data['D009_Id']);
				$('#' + divId + ' #T008_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
				$('#' + divId + ' #T008_Flag_Destino_Produto').val(request.data['T007_Flag_Destino_Produto']);
				$('#' + divId + ' #D001_Id').val(request.data['D001_Id']);
				$('#' + divId + ' #T008_Valor_Preco_Sem_Desconto_Unitario').val(request.data['Preco_Produto']);
				$('#' + divId + ' #T008_Aliquota_IPI').val(request.data['Aliquota_IPI']);
				$('#' + divId + ' #T008_D006_Id').val(request.data['D006_Id']);
				$('#' + divId + ' #Codigo_Origem_Mercadoria').val(request.data['D049_Origem_Mercadoria']);
				$('#' + divId + ' #T008_Percentual_Desconto').val(request.data['T008_Percentual_Desconto']);
				if(request.data['D001_Codigo_Produto']=='55555'){
					$('#' + divId + ' #T008_Descricao_Produto').removeAttr('readonly');
				}
				// Focus
				$('#ven015FormProdutosPDVBtnAdd').focus();
				$('#ven015FormProdutosPDVBtnAdd').click();
				ven015FormProdutosPDVBtnAdd
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				dialogConfirm('Erro', request.data, $buttons);
			}
		});
	}

	function ProcuraProdutoPesquisaFocus() {
		$('#Procura_Produto_Pesquisa').focus();
	}	

	function exibeMensagem(dialog) {
		if (dialog) {
			var buttons = {};
			if (dialog['buttons']) {
				buttons = dialog['buttons'];
			}

			if (Object.keys(buttons).length > 0) {
				dialogConfirm(dialog['titulo'], dialog['msg'], buttons);
			} else {
				dialogConfirm(dialog['titulo'], dialog['msg']);
			}
		}
	}

} </script></div>
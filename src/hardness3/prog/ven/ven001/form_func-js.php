<div class="divHidden">
<script type="text/javascript"> if (typeof jQuery != 'undefined'){

function ven001CampoProcuraProduto(divId, D009_Id, D049_Flag_Tipo, D144_Id, T003_Id, D024_Id, habilitarInclusaoSubItens, pesquisa) {
	
	if(habilitarInclusaoSubItens == 'S' && D049_Flag_Tipo == 'MT'){
		var buttons = {
			"Incluir sub-itens": function() {
				abrirJanela(false, divIdRootAberto(), divId, unique(), '', 'Orçamento - Incluir sub-itens produto montado', '/ven/ven001/content/ven001ContentIncluirItemMontado/', '&D009_Id=' + encodeURIComponent(D009_Id) + '&T003_Id=' + encodeURIComponent(T003_Id) + '&divForm=' + encodeURIComponent(divId), [700,500]);
				$(this).dialog("destroy").remove();
			},
			"Incluir item principal": function() {
				_ven001CampoProcuraProduto(divId, D009_Id, D144_Id, T003_Id, D024_Id, pesquisa);
				$(this).dialog("destroy").remove();
			}
		}
		var dialog = dialogConfirm('Inclusão de produto', 'Este produto é montado, selecione o modo que deseja incluir o produto', buttons);
	} else {
		_ven001CampoProcuraProduto(divId, D009_Id, D144_Id, T003_Id, D024_Id, pesquisa);
	}
}
	function _ven001CampoProcuraProduto(divId, D009_Id, D144_Id, T003_Id, D024_Id, pesquisa){
		$('#' + divId).showLoading();
		$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposProdutos/?ajax=true&T003_Id=' + encodeURIComponent(T003_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&pesquisa=' + encodeURIComponent(pesquisa) + '&callback=?', function(request) {
			if (request.code) {
				// Preenche os campos
				//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
				$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
				$('#' + divId + ' #T004_D037_Id').val(request.data['D001_D037_Id']);
				$('#' + divId + ' #T004_D005_Id').val(request.data['D001_D005_Id']);
				$('#' + divId + ' #T004_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
				$('#' + divId + ' #T004_Numero_Pedido_Compra').val(request.data['T004_Numero_Pedido_Compra']);
				$('#' + divId + ' #T004_D009_Id').val(request.data['D009_Id']);
				$('#' + divId + ' #T004_D006_Id').val(request.data['D006_Id']);
				$('#' + divId + ' #T004_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
				$('#' + divId + ' #T004_Flag_Destino_Produto').val(request.data['T003_Flag_Destino_Produto']);
				$('#' + divId + ' #T004_Percentual_Desconto').val(request.data['T004_Percentual_Desconto']);
				$('#' + divId + ' #D001_Id').val(request.data['D001_Id']);

				$('#' + divId + ' #D001_Peso_Unitario_Bruto').val(request.data['D001_Peso_Unitario_Bruto']);
				$('#' + divId + ' #D001_Qte_Embalagem').val(request.data['D001_Qte_Embalagem']);

				$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
				//$('#' + divId + ' #NcmProduto').val(request.data['D001_D005_Id']);
				$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);
				$('#' + divId + ' #T004_Observacao_Entrega').val(request.data['T003_Prazo_Entrega']);

				$('#' + divId + ' #T004_Valor_Preco_Sem_Desconto_Unitario').val(request.data['Preco_Produto']);
				$('#' + divId + ' #PrecoMinimo').val(request.data['PrecoMinimo']);
				$('#' + divId + ' #T004_IPV').val(request.data['IPV']);
				//$('#' + divId + ' #T004_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
				$('#' + divId + ' #T004_Aliquota_IPI').val(request.data['Aliquota_IPI']);
				
				$('#' + divId + ' #UltimoPrecoVenda').css('font-family','Courier New');
				$('#' + divId + ' #UltimoPrecoVenda').css('font-size','11px');
				$('#' + divId + ' #UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);
				$('#' + divId + ' #T004_D144_Id').val(D144_Id);

				$('#' + divId + ' #estoqueLiquido').val(request.data['estoqueLiquido']);
				$('#' + divId + ' #estoqueLoja').val(request.data['estoqueLoja']);
				$('#' + divId + ' #estoqueTotal').val(request.data['estoqueTotal']);
				$('#' + divId + ' #ordemCompra').val(request.data['ordemCompra']);
				$('#' + divId + ' #estoqueFora').val(request.data['estoqueFora']);
				$('#' + divId + ' #precoTabela').val(request.data['precoTabela']);
				$('#' + divId + ' #precoTabela').css('color', 'black');
				$('#' + divId + ' #D001_Peso_Unitario_Kg').val(request.data['D001_Peso_Unitario_Kg']);
				$('#' + divId + ' #D001_Qte_Embalagem_disabled').val(request.data['D001_Qte_Embalagem']);
				$('#' + divId + ' #D049_Peso_Unitario_Kg').val(request.data['D049_Peso_Unitario_Kg']);
				if(request.data['validadePrecoTabela'] != 'true'){
					$('#' + divId + ' #precoTabela').css('color', 'red');
				}

				if (T004_Codigo_Local!='') {
					$('#ambiente').css('background','blue');
					$('#ambiente').css('color','white');
					$('#ambiente').val(T004_Codigo_Local);
				} else {
					$('#ambiente').css('background','white');
					$('#ambiente').css('color','black');
				}

				$('#' + divId + ' #estoqueLiquido').css('color', 'black');
				if(request.data['estoqueLiquido2'] <= 0){
					$('#' + divId + ' #estoqueLiquido').css('color', 'red');
				}
				$('.formEditor-infoCustoVenda').html(request.data['infoCustoVenda']);

				if(request.data['D001_Codigo_Produto']=='55555'){
					$('#' + divId + ' #T004_Descricao_Produto').removeAttr('readonly');
				}

				if (request.data['utilizaQuantidadeConversao']) {
					$('#' + divId + ' #usaConversao').val(request.data['D002A_Utiliza_Conversao']);
					$('#' + divId + ' #T004_Quantidade').val('');
					$('#' + divId + ' #T004A_Quantidade_Conversao').val('');

					if (request.data['D002A_Utiliza_Conversao'] === 'S') {
						if (request.data['campoFocus'] == 'T004_Quantidade') {
							request.data['campoFocus'] = 'T004A_Quantidade_Conversao';
						}

						$('#' + divId + ' #T004_Quantidade').attr('readonly',            true);
						$('#' + divId + ' #T004A_Quantidade_Conversao').attr('readonly', false);
					} else {
						$('#' + divId + ' #T004_Quantidade').attr('readonly',            false);
						$('#' + divId + ' #T004A_Quantidade_Conversao').attr('readonly', true);
					}
				}

				$('#' + divId + ' #T004_Quantidade').val(request.data['SMT_Quantidade_Sugerida']); // Retono Smartness: Pesonalização IDE 
				
				if(request.data['SMT_Descricao'] === 'S'){
					if (request.data['D001A_Flag_Generico'] === 'S') {
						$('#' + divId + ' #T004_Descricao_Produto').attr('readonly',false);
					}else {
						$('#' + divId + ' #T004_Descricao_Produto').attr('readonly',true);
					}
				}
					

				campoProcuraLocacao(D009_Id,divId);

				// Focus
				$('#' + divId + ' #' + request.data['campoFocus']).focus();

				if (request.data['SMT_Incluir_Auto'] === 'S') {
					$('#ven001FormProdutosBtnAdd').focus();
					$('#ven001FormProdutosBtnAdd').click();
					ven001FormProdutosBtnAdd
				}

			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    		dialogConfirm('Erro', request.data, $buttons);
			}
			$('#' + divId).hideLoading();
		});
	}

	function campoProcuraLocacao(D009_Id, divId) {

		setTimeout(function() {
			$.getJSON('/ven/ven001/form_func-ajax/carregarLocacoes/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
				if (request.code) {
					// Locações
					if ($('#T004A_T066_Id').size() > 0) { $('#T004A_T066_Id').html(request.data['T004A_T066_Id']); } 
					var T004A_T066_Id_refreshBtn = $('#T004A_T066_Id_refreshBtn').attr('onclick');
					var matches = T004A_T066_Id_refreshBtn.match(/, '[^']+'/g);
					T004A_T066_Id_refreshBtn = T004A_T066_Id_refreshBtn.replace(matches[1], ", '" + request.data['T004A_T066_Id_refreshBtn'] + "'");
					$('#T004A_T066_Id_refreshBtn').attr('onclick', T004A_T066_Id_refreshBtn);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
					dialogConfirm("Erro!", request.data['erro'], $buttons)
				}
			});
		}, 100);
	}

	function campoProcuraProdutoAssociarItemOrc(divId, D009_Id) {
		$('#' + divId).showLoading();
		$.getJSON('/ven/ven001/form_func-ajax/campoProcuraProdutoAssociarItemOrc/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
			if (request.code) {
				$('#' + divId + ' #Procura_Produto_Associar_Pesquisa').val(request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto']);
				$('#' + divId + ' #D001_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
				$('#' + divId + ' #D009_Id').val(request.data['D009_Id']);			
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				dialogConfirm('Erro', request.data, $buttons);
			}
			$('#' + divId).hideLoading();
		});
	}

	function ven001CampoProcuraProdutoGrid(divId, D009_Id, D144_Id, T003_Id){
	
		$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposProdutos/?ajax=true&T003_Id=' + encodeURIComponent(T003_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
			if (request.code) {
				// Preenche os campos
				//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
				$('#Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
				$('#T004_D037_Id').val(request.data['D001_D037_Id']);
				$('#T004_D005_Id').val(request.data['D001_D005_Id']);
				$('#T004_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
				$('#T004_Numero_Pedido_Compra').val(request.data['T004_Numero_Pedido_Compra']);
				$('#T004_D009_Id').val(request.data['D009_Id']);
				$('#T004_D006_Id').val(request.data['D006_Id']);
				$('#T004_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
				$('#T004_Flag_Destino_Produto').val(request.data['T003_Flag_Destino_Produto']);
				$('#D001_Id').val(request.data['D001_Id']);

				$('#Marca').val(request.data['D082_Marca']);
				//$('#NcmProduto').val(request.data['D001_D005_Id']);
				$('#T004_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);
				$('#T004_Observacao_Entrega').val(request.data['T003_Prazo_Entrega']);

				$('#T004_Valor_Preco_Sem_Desconto_Unitario').val(request.data['Preco_Produto']);
				$('#PrecoMinimo').val(request.data['PrecoMinimo']);
				$('#T004_IPV').val(request.data['IPV']);
				//$('#T004_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
				$('#T004_Aliquota_IPI').val(request.data['Aliquota_IPI']);
				$('#UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);
				$('#T004_D144_Id').val(D144_Id);

				$('#estoqueLiquido').val(request.data['estoqueLiquido']);
				$('#estoqueLoja').val(request.data['estoqueLoja']);
				$('#estoqueTotal').val(request.data['estoqueTotal']);
				$('#ordemCompra').val(request.data['ordemCompra']);
				$('#estoqueFora').val(request.data['estoqueFora']);
				$('#precoTabela').val(request.data['precoTabela']);
				$('#precoTabela').css('color', 'black');
				if(request.data['validadePrecoTabela'] != 'true'){
					$('#precoTabela').css('color', 'red');
				}

				$('#estoqueLiquido').css('color', 'black');
				if(request.data['estoqueLiquido2'] <= 0){
					$('#estoqueLiquido').css('color', 'red');
				}
				$('.formEditor-infoCustoVenda').html(request.data['infoCustoVenda']);

				if(request.data['D001_Codigo_Produto']=='55555'){
					$('#T004_Descricao_Produto').removeAttr('readonly');
				}

				// Focus
				$('#' + request.data['campoFocus']).focus();
				fecharJanela(divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    		dialogConfirm('Erro', request.data, $buttons);
			}
			
		});
	}
}

	function T004_Quantidade_Focus() {
		$('#T004_Quantidade').focus();
	}	

	function calculaQuantidadeConversao(elem, divId) {
		var quantidade    = 0.00;
		var qtdeConversao = parseFloat($('#' + divId + ' #T004A_Quantidade_Conversao').val());
		var pesoLiquido   = parseFloat($('#' + divId + ' #D001_Peso_Unitario_Kg').val());

		if ((qtdeConversao > 0) && (pesoLiquido > 0)) {
			quantidade = qtdeConversao * pesoLiquido;
		}

		var quantidadeStr = quantidade.toLocaleString("pt-BR");
		$('#' + divId + ' #T004_Quantidade').val(quantidadeStr);
	}

	function gatilhoPrecoOrcamentoPorQtd(elem, T004_Quantidade ,T004_T003_Id ,T004_D009_Id , estoqueLiquido, divId) {

		$.getJSON('/ven/ven001/form_func-ajax/gatilhoPrecoOrcamentoPorQtd/?ajax=true&T004_Quantidade=' + encodeURIComponent(T004_Quantidade) + '&D009_Id=' + encodeURIComponent(T004_D009_Id) + '&T003_Id=' + encodeURIComponent(T004_T003_Id) + '&estoqueLiquido=' + encodeURIComponent(estoqueLiquido) + '&callback=?', function(request) {
			if (request.code) {
				$('#' + divId + ' #T004_Valor_Preco_Sem_Desconto_Unitario').val(request.data['precoProduto']);
			}
		});

	}

	function calculaQuantidade(elem, divId) {
		var usaConversao = Boolean($('#' + divId + ' #usaConversao').val() == 'S');

		if (!usaConversao) {
			var quantidadeConversao = parseFloat($('#' + divId + ' #T004_Quantidade').val());

			var quantidadeStr = quantidadeConversao.toLocaleString("pt-BR");
			$('#' + divId + ' #T004A_Quantidade_Conversao').val(quantidadeStr);
		}
	}

	

	</script>
</div>

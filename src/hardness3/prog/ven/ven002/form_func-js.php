<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraProduto(divId, D009_Id, D144_Id, T005_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	//console.log(divId + ' - ' + D009_Id + ' - ' + T005_Id);
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven002/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T005_Id='+ encodeURIComponent(T005_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			//console.log('#' + divId + ' #Procura_Produto_Pesquisa');
			$('#' + divId + ' #T006_D037_Id').val(request.data['D001_D037_Id']);
			$('#' + divId + ' #T006_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #T006_D006_Id').val(request.data['D006_Id']);
			$('#' + divId + ' #T006_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T006_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T006_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #T006_Numero_Pedido_Compra').val(request.data['T006_Numero_Pedido_Compra']);
			$('#' + divId + ' #T006_Flag_Destino_Produto').val(request.data['T005_Flag_Destino_Produto']);
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			$('#' + divId + ' #D001_Id').val(request.data['D001_Id']);
			$('#' + divId + ' #Marca_disabled').val(request.data['D082_Marca']);
			$('#' + divId + ' #T006_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);	
			$('#' + divId + ' #T006_Valor_Preco_Sem_Desconto_Unitario').val(request.data['T006_Valor_Preco_Sem_Desconto_Unitario']);
			$('#T006_Percentual_Desconto').val(request.data['T006_Percentual_Desconto']);
			$('#' + divId + ' #PrecoMinimo').val(request.data['PrecoMinimo']);
			//$('#' + divId + ' #T006_IPV').val(request.data['IPV']);
			//$('#' + divId + ' #T006_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			$('#' + divId + ' #T006_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId + ' #UltimoPrecoVenda').css('font-family','Courier New');
			$('#' + divId + ' #UltimoPrecoVenda').css('font-size','11px');
			$('#' + divId + ' #UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);
			$('#' + divId + ' #D001_Peso_Unitario_Kg').val(request.data['D001_Peso_Unitario_Kg']);
			$('#' + divId + ' #D001_Qte_Embalagem_disabled').val(request.data['D001_Qte_Embalagem']);
			$('#' + divId + ' #D049_Peso_Unitario_Kg').val(request.data['D049_Peso_Unitario_Kg']);

			$('#' + divId + ' #D001_Peso_Unitario_Bruto').val(request.data['D001_Peso_Unitario_Bruto']);
			$('#' + divId + ' #D001_Qte_Embalagem').val(request.data['D001_Qte_Embalagem']);

			$('#' + divId + ' #T006_D144_Id').val(D144_Id);
			$('#' + divId + ' #precoTabela').css('color', 'black');
			if(request.data['validadePrecoTabela'] != 'true'){
				$('#' + divId + ' #precoTabela').css('color', 'red');
			}

			if (T006_Codigo_Local!='') {
				$('#ambiente').css('background','blue');
				$('#ambiente').css('color','white');
				$('#ambiente').val(T006_Codigo_Local);
			} else {
				$('#ambiente').css('background','white');
				$('#ambiente').css('color','black');
			}

			$('#' + divId + ' #T006_D022_Id').val(request.data['T005_D022_Id']);
			$('#' + divId + ' #T006_T208_Id').val(request.data['T005_T208_Id']);

			$('#' + divId + ' #estoqueLiquido').val(request.data['estoqueLiquido']);
			$('#' + divId + ' #estoqueLoja').val(request.data['estoqueLoja']);
			$('#' + divId + ' #estoqueTotal').val(request.data['estoqueTotal']);
			$('#' + divId + ' #ordemCompra').val(request.data['ordemCompra']);
			$('#' + divId + ' #estoqueFora').val(request.data['estoqueFora']);
			$('#' + divId + ' #precoTabela').val(request.data['precoTabela']);
			
			$('#' + divId + ' #estoqueLiquido').css('color', 'black');
			if(request.data['estoqueLiquido2'] <= 0){
				$('#' + divId + ' #estoqueLiquido').css('color', 'red');
			}

			if (request.data['utilizaQuantidadeConversao']) {
				$('#' + divId + ' #usaConversao').val(request.data['D002A_Utiliza_Conversao']);
				$('#' + divId + ' #T006_Quantidade').val('');
				$('#' + divId + ' #T006A_Quantidade_Conversao').val('');

				if (request.data['D002A_Utiliza_Conversao'] === 'S') {
					if (request.data['campoFocus'] == 'T006_Quantidade') {
						request.data['campoFocus'] = 'T006A_Quantidade_Conversao';
					}

					$('#' + divId + ' #T006_Quantidade').attr('readonly',            true);
					$('#' + divId + ' #T006A_Quantidade_Conversao').attr('readonly', false);
				} else {
					$('#' + divId + ' #T006_Quantidade').attr('readonly',            false);
					$('#' + divId + ' #T006A_Quantidade_Conversao').attr('readonly', true);
				}
			}
			$('#' + divId + ' #T006_Quantidade').val(request.data['SMT_Quantidade_Sugerida']); // Retono Smartness: Pesonalização IDE 

			if(request.data['SMT_Descricao'] === 'S'){
				if (request.data['D001A_Flag_Generico'] === 'S') {
						$('#' + divId + ' #T006_Descricao_Produto').attr('readonly',false);
					}else {
						$('#' + divId + ' #T006_Descricao_Produto').attr('readonly',true);
				}
			}
			// Focus
			$('#' + divId + ' #' + request.data['campoFocus']).focus();
			if (request.data['SMT_Incluir_Auto'] === 'S') {
				$('#ven002FormProdutosBtnAdd').focus();
				$('#ven002FormProdutosBtnAdd').click();
				ven002FormProdutosBtnAdd
			}
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

//Essa função foi utilizada para poder ser acessada pelos Grid's
//EX: desenvolvido para grid de similares, ao clicar na linha do produto os dados abaixo sao puxados para o formulario dentro do editar produto
function campoProcuraProdutoGrid(divId, D009_Id, D144_Id, T005_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	//console.log(divId + ' - ' + D009_Id + ' - ' + T005_Id);
	
	$.getJSON('/ven/ven002/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T005_Id='+ encodeURIComponent(T005_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			$('#Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			//console.log('#Procura_Produto_Pesquisa');
			$('#T006_D037_Id').val(request.data['D001_D037_Id']);
			$('#T006_D005_Id').val(request.data['D001_D005_Id']);
			$('#T006_D006_Id').val(request.data['D006_Id']);
			$('#T006_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#T006_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#T006_D009_Id').val(request.data['D009_Id']);
			$('#T006_Numero_Pedido_Compra').val(request.data['T006_Numero_Pedido_Compra']);
			$('#T006_Flag_Destino_Produto').val(request.data['T005_Flag_Destino_Produto']);
			$('#Marca').val(request.data['D082_Marca']);
			$('#D001_Id').val(request.data['D001_Id']);
			$('#Marca_disabled').val(request.data['D082_Marca']);
			$('#T006_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);	
			$('#T006_Valor_Preco_Sem_Desconto_Unitario').val(request.data['T006_Valor_Preco_Sem_Desconto_Unitario']);
			$('#PrecoMinimo').val(request.data['PrecoMinimo']);
			//$('#T006_IPV').val(request.data['IPV']);
			//$('#T006_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			$('#T006_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);
			$('#T006_D144_Id').val(D144_Id);
			$('#precoTabela').css('color', 'black');
			if(request.data['validadePrecoTabela'] != 'true'){
				$('#precoTabela').css('color', 'red');
			}
			$('#T006_D022_Id').val(request.data['T005_D022_Id']);
			$('#T006_T208_Id').val(request.data['T005_T208_Id']);

			$('#estoqueLiquido').val(request.data['estoqueLiquido']);
			$('#estoqueLoja').val(request.data['estoqueLoja']);
			$('#estoqueTotal').val(request.data['estoqueTotal']);
			$('#ordemCompra').val(request.data['ordemCompra']);
			$('#estoqueFora').val(request.data['estoqueFora']);
			$('#precoTabela').val(request.data['precoTabela']);
			
			$('#estoqueLiquido').css('color', 'black');
			if(request.data['estoqueLiquido2'] <= 0){
				$('#estoqueLiquido').css('color', 'red');
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

function campoProcuraProdutoEditar(divId, D009_Id, T005_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven002/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T005_Id='+ encodeURIComponent(T005_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Edicao_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' .itemForm-T006_D037_Id').val(request.data['D001_D037_Id']);
			var divIdIPI = $('#' + divId).parent().find('.ui-tabs-panel:eq(1)').attr('id');
			$('#' + divIdIPI + ' .itemForm-T006_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' .itemForm-T006_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' .itemForm-T006_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' .itemForm-T006_D009_Id').val(request.data['D009_Id']);			
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			$('#' + divId + ' #Marca_disabled').val(request.data['D082_Marca']);
			$('#' + divId + ' .itemForm-T006_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);	
			$('#' + divId + ' .itemForm-T006_Valor_Preco_Sem_Desconto_Unitario').val(request.data['T006_Valor_Preco_Sem_Desconto_Unitario']);
			$('#' + divId + ' .itemForm-T006_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId + ' #UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);
			$('#' + divId + ' .itemForm-T006_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function ven002CampoProcuraProdutoTroca(divId, D009_Id, T005_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven002/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T005_Id='+ encodeURIComponent(T005_Id) + '&callback=?', function(request) {
		console.log(divId);
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #Procura_Produto_Troca_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T138_D009_Id').val(request.data['D009_Id']);						
			$('#' + divId + ' #Marca_Produto').val(request.data['D082_Marca']);	
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);		
			$('#' + divId + ' #T138_Valor_Preco_Unitario').val(request.data['D041_Valor_Base']);
			// Focus
			$('#' + divId + ' #T138_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraProdutoMaterial(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId + ' #ven002FormMateriaisPesquisa_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T149_D037_Id').val(request.data['D001_D037_Id']); 
			//$('#' + divId + ' #T004_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T149_D009_Id').val(request.data['D009_Id']);
			//$('#' + divId + ' #T004_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			
			$('#' + divId + ' #ven002_Marca').val(request.data['D082_Marca']);
			//$('#' + divId + ' #NcmProduto').val(request.data['D001_D005_Id']);
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);			
			
			//$('#' + divId + ' #T004_Valor_Preco_Sem_Desconto_Unitario').val(request.data['D041_Valor_Base']);
			//$('#' + divId + ' #T004_IPV').val(request.data['IPV']);
			//$('#' + divId + ' #T004_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			//$('#' + divId + ' #T004_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			
			// Focus
			$('#' + divId + ' #T147_Descricao').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraServicoPedido(divId, D112_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposServicos/?ajax=true&D112_Id=' + encodeURIComponent(D112_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T006_Codigo_Substituto').val(request.data['T006_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Servico_Pesquisa').val(request.data['D112_Titulo']);
			$('#' + divId + ' #T147_Titulo').val(request.data['D112_Titulo']);
			$('#' + divId + ' #T147_T212_Id').html(request.data['selectTipoServico']);
			$('#' + divId + ' #T147_D112_Id').val(request.data['D112_Id']);
			// Focus
			$('#' + divId + ' #T147_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
var idDialog = 0;
function carregaCFOP_Contatos(elem, divId, T005_C004_Id_Estoque, alterarVendedorAusente, alterarVendedor) {
	if (!isempty($('#T005_D024_Id').val()) && $('#T005_D024_Id').val() != 0) {
		var idVendedorInterno = $('#T005_C007_Id_Vendedor_Interno').val();
	    $('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/ven/ven002/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T005_D024_Id=' + encodeURIComponent($('#T005_D024_Id').val()) + '&T005_C004_Id_Estoque=' + encodeURIComponent(T005_C004_Id_Estoque) + '&T005_C007_Id_Vendedor_Interno=' + encodeURIComponent(idVendedorInterno) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
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
					$('#T005_D006_Id').html(request.data['T005_D006_Id']);
					$('#T005_C007_Id_Vendedor_Interno').val(request.data['T005_C007_Id_Vendedor_Interno']);
					$('#T005_C007_Id_Vendedor_Externo').val(request.data['T005_C007_Id_Vendedor_Externo']);
					$('#T005_Flag_Revenda_Consumidor').val(request.data['T005_Flag_Revenda_Consumidor']);
					if(request.data['T005_Flag_ACP']){
						$('#T005_Flag_ACP').val(request.data['T005_Flag_ACP']);
					}
					if(request.data['setaDestinoVenda']){
						$('#T005_Flag_Destino_Produto').val(request.data['T005_Flag_Destino_Produto']);
					}
					$('#T005_Flag_Expedicao').html(request.data['T005_Flag_Expedicao']);
					if ($('#T005_D021_Id').size() > 0) { $('#T005_D021_Id').html(request.data['T005_D021_Id']); } 
					$('.itemForm-T005_C007_Id_Vendedor_Interno').attr('onfocus', 'this.initialSelect = this.selectedIndex;').attr('onchange', 'this.selectedIndex = this.initialSelect;').attr('readonly', 'readonly');
					if(request.data['notificarVendedor'] == 1){
						$('.itemForm-T005_C007_Id_Vendedor_Interno').css('border', '1px solid #023adf');
					} else if(request.data['notificarVendedor'] == 2){
						$('.itemForm-T005_C007_Id_Vendedor_Interno').css('border', '1px solid #E99601');
						if(alterarVendedorAusente == 'S'){
							$('.itemForm-T005_C007_Id_Vendedor_Interno').removeAttr('onfocus').removeAttr('onchange').removeAttr('readonly');
						}
					}
					if(alterarVendedor == 'S'){
						$('.itemForm-T005_C007_Id_Vendedor_Interno').removeAttr('onfocus').removeAttr('onchange').removeAttr('readonly');
					}

					$('#T005_D022_Id').val(request.data['T005_D022_Id']);
					$('#T005_D022_Id_Pesquisa').val(request.data['T005_D022_Id_Pesquisa']);
					$('#T005_Flag_Frete').val(request.data['T005_Flag_Frete']);

					// Contatos
					$('#T005_D013_Id').html(request.data['T005_D013_Id']);
					var T005_D013_Id_refreshBtn = $('#T005_D013_Id_refreshBtn').attr('onclick');
					var matches = T005_D013_Id_refreshBtn.match(/, '[^']+'/g);
					T005_D013_Id_refreshBtn = T005_D013_Id_refreshBtn.replace(matches[1], ", '" + request.data['T005_D013_Id_refreshBtn'] + "'");
					$('#T005_D013_Id_refreshBtn').attr('onclick', T005_D013_Id_refreshBtn);

					// endereco entrega
					if ($('#T005_D148_Id_Entrega').size() > 0) { $('#T005_D148_Id_Entrega').html(request.data['T005_D148_Id_Entrega']); } 
					var T005_D148_Id_Entrega_refreshBtn = $('#T005_D148_Id_Entrega_refreshBtn').attr('onclick');
					var matches = T005_D148_Id_Entrega_refreshBtn.match(/, '[^']+'/g);
					T005_D148_Id_Entrega_refreshBtn = T005_D148_Id_Entrega_refreshBtn.replace(matches[1], ", '" + request.data['T005_D148_Id_Entrega_refreshBtn'] + "'");
					$('#T005_D148_Id_Entrega_refreshBtn').attr('onclick', T005_D148_Id_Entrega_refreshBtn);

					// endereco cobranca
					if ($('#T005_D148_Id_Cobranca').size() > 0) { $('#T005_D148_Id_Cobranca').html(request.data['T005_D148_Id_Cobranca']); } 
					var T005_D148_Id_Cobranca_refreshBtn = $('#T005_D148_Id_Cobranca_refreshBtn').attr('onclick');
					var matches = T005_D148_Id_Cobranca_refreshBtn.match(/, '[^']+'/g);
					T005_D148_Id_Cobranca_refreshBtn = T005_D148_Id_Cobranca_refreshBtn.replace(matches[1], ", '" + request.data['T005_D148_Id_Cobranca_refreshBtn'] + "'");
					$('#T005_D148_Id_Cobranca_refreshBtn').attr('onclick', T005_D148_Id_Cobranca_refreshBtn);

					// endereco redespacho
					if ($('#T005A_D148_Id_Redespacho').size() > 0) { $('#T005A_D148_Id_Redespacho').html(request.data['T005A_D148_Id_Redespacho']); } 
					var T005A_D148_Id_Redespacho_refreshBtn = $('#T005A_D148_Id_Redespacho_refreshBtn').attr('onclick');
					var matches = T005A_D148_Id_Redespacho_refreshBtn.match(/, '[^']+'/g);
					T005A_D148_Id_Redespacho_refreshBtn = T005A_D148_Id_Redespacho_refreshBtn.replace(matches[1], ", '" + request.data['T005A_D148_Id_Redespacho_refreshBtn'] + "'");
					$('#T005A_D148_Id_Redespacho_refreshBtn').attr('onclick', T005A_D148_Id_Redespacho_refreshBtn);
					
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
	    			dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}, 100);
	}
}

function carregaListaPreco(elem, divId){
	$('#' + divId).showLoading();
    setTimeout(function() {
	    $.getJSON('/ven/ven002/form_func-ajax/carregaListaPreco/?ajax=true&T005_D024_Id=' + encodeURIComponent($('#T005_D024_Id').val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#T005_Flag_Lista_Preco').html(request.data['T005_Flag_Lista_Preco']);
				reloadUI('#' . divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    			dialogConfirm("Erro!", request.data, $buttons)
			}
		});
	}, 100);	
}
function gravarDescontoPedido(elem, divId, T005_Id, valor) {   
	//if (!isempty($('#T005_Valor_Desconto').val()) && $('#T005_Valor_Desconto').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/ven/ven002/form_func-ajax/gravarDescontoPedido/?ajax=true&T005_Valor_Desconto=' + encodeURIComponent($('#T005_Valor_Desconto').val()) + '&T005_Id=' + encodeURIComponent(T005_Id) + '&Valor=' + encodeURIComponent(valor) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					// $('#T003_D006_Id').html(request.data['T003_D006_Id']);
					// $('#T003_D013_Id').html(request.data['T003_D013_Id']); 
					// $('#T003_Flag_Revenda_Consumidor').val(request.data['T003_Flag_Revenda_Consumidor']);
					// $('#T003_C007_Id_Vendedor_Interno').val(request.data['T003_C007_Id_Vendedor_Interno']);
					divRefresh(divId);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}, 100);
	//}
}

function copiaTransportadora(idTipoTransp){
	$('#T005_D022_Id').val($('#TransportadoraPadrao_disabled').val());
    $('#T005_D022_Id_Pesquisa').val($("#TransportadoraPadrao_disabled option:selected").text());
    carregaTipoTransp();
}

function carregaTipoTransp(elem, divId) {
    setTimeout(function() {
        $.getJSON('/ven/ven002/form_func-ajax/carregaTipoTransp/?ajax=true&D022_Id=' + encodeURIComponent($('#T005_D022_Id').val()) + '&callback=?', function(request) {
            if (request.code) {
                $('#T005_D036_Id').val(request.data['T005_D036_Id']);
            } else {
                var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
                dialogConfirm("Erro!", request.data, $buttons)
            }
        });
    }, 100);
}

function alteraServico(combobox, divId) {
	var D112_Id = combobox.value;
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarDadosServicos/?ajax=true&D112_Id=' + encodeURIComponent(D112_Id) + '&callback=?', function(request) {
		if (request.code) {
			//Preenche os campos
			$('#' + divId + ' #T147_Descricao').val(request.data['D112_Descricao']);
			$('#' + divId + ' #T147_Tempo_Estimado').val(request.data['D112_Tempo_Estimado']);
			$('#' + divId + ' #T147_Valor_Unitario').val(request.data['D112_Valor_Unitario']);
			$('#' + divId + ' #T147_Valor_Total').val(request.data['D112_Valor_Unitario']);
			$('#' + divId + ' #T147_D110_Id').val(request.data['D112_D110_Id']);
			$('#' + divId + ' #T147_D112_Id').val(request.data['D112_Id']);
			$('#' + divId + ' #T147_T212_Id').val(request.data['T212_Id']);
			$('#' + divId + ' #T147_Titulo').val(request.data['D112_Titulo']);
			$('#' + divId + ' #Procura_Tipo_Pesquisa').val(request.data['T212_Nome']);
			// Focus
			$('#' + divId + ' #T147_Descricao').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function T006_Quantidade_Focus() {
	$('#T006_Quantidade').focus();
}	

function calculaQuantidadeConversao(elem, divId) {
	var quantidade    = 0.00;
	var qtdeConversao = parseFloat($('#' + divId + ' #T006A_Quantidade_Conversao').val());
	var pesoLiquido   = parseFloat($('#' + divId + ' #D001_Peso_Unitario_Kg').val());

	if ((qtdeConversao > 0) && (pesoLiquido > 0)) {
		quantidade = qtdeConversao * pesoLiquido;
	}

	var quantidadeStr = quantidade.toLocaleString("pt-BR");
	$('#' + divId + ' #T006_Quantidade').val(quantidadeStr);
}

function calculaQuantidade(elem, divId) {
	var usaConversao = Boolean($('#' + divId + ' #usaConversao').val() == 'S');

	if (!usaConversao) {
		var quantidadeConversao = parseFloat($('#' + divId + ' #T006_Quantidade').val());

		var quantidadeStr = quantidadeConversao.toLocaleString("pt-BR");
		$('#' + divId + ' #T006A_Quantidade_Conversao').val(quantidadeStr);
	}
}

function gatilhoPrecoPedidoPorQtd(elem, T006_Quantidade ,T006_T005_Id ,T006_D009_Id , estoqueLiquido, divId) {
	$.getJSON('/ven/ven002/form_func-ajax/gatilhoPrecoPedidoPorQtd/?ajax=true&T006_Quantidade=' + encodeURIComponent(T006_Quantidade) + '&D009_Id=' + encodeURIComponent(T006_D009_Id) + '&T005_Id=' + encodeURIComponent(T006_T005_Id) + '&estoqueLiquido=' + encodeURIComponent(estoqueLiquido) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #T006_Valor_Preco_Sem_Desconto_Unitario').val(request.data['precoProduto']);
		}
	});
}

function calcularValorTroco(divId) {
	var valorRecebidoStr = parseFloat(document.getElementById("ValorRecebido").value.replace('.', '').replace(',', '.'));
	var valorTotalStr    = parseFloat(document.getElementById("T005_Valor_Total").value.replace('.', '').replace(',', '.'));
	var valorRecebido    = Number.isNaN(Number.parseFloat(valorRecebidoStr)) ? 0 : Number.parseFloat(valorRecebidoStr);
	var valorTotal       = Number.isNaN(Number.parseFloat(valorTotalStr)) ? 0 : Number.parseFloat(valorTotalStr);
	var valorTroco       = valorRecebido - valorTotal;
	
	document.getElementById("ValorTroco").value = valorTroco.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL', minimumFractionDigits: 2});

	if (valorTroco < 0) {
		$('#ValorTroco').css("color", "red");
	} else {
		$('#ValorTroco').css("color", "green");
	}
}

} </script>
</div>

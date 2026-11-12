<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraProduto(divId, D009_Id, D144_Id, D157_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	//console.log(divId + ' - ' + D009_Id + ' - ' + D157_Id);
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad061/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&D157_Id='+ encodeURIComponent(D157_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			//console.log('#' + divId + ' #Procura_Produto_Pesquisa');
			$('#' + divId + ' #D158_D037_Id').val(request.data['D001_D037_Id']);
			$('#' + divId + ' #D158_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #D158_D006_Id').val(request.data['D006_Id']);
			$('#' + divId + ' #D158_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #D158_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #D158_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #D158_Numero_Pedido_Compra').val(request.data['D158_Numero_Pedido_Compra']);
			$('#' + divId + ' #D158_Flag_Destino_Produto').val(request.data['D157_Flag_Destino_Produto']);
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			$('#' + divId + ' #D001_Id').val(request.data['D001_Id']);
			$('#' + divId + ' #Marca_disabled').val(request.data['D082_Marca']);
			$('#' + divId + ' #D158_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);	
			$('#' + divId + ' #D158_Valor_Preco_Sem_Desconto_Unitario').val(request.data['D158_Valor_Preco_Sem_Desconto_Unitario']);
			$('#' + divId + ' #PrecoMinimo').val(request.data['PrecoMinimo']);
			//$('#' + divId + ' #D158_IPV').val(request.data['IPV']);
			//$('#' + divId + ' #D158_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			$('#' + divId + ' #D158_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId + ' #UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);
			$('#' + divId + ' #D158_D144_Id').val(D144_Id);
			$('#' + divId + ' #precoTabela').css('color', 'black');
			if(request.data['validadePrecoTabela'] != 'true'){
				$('#' + divId + ' #precoTabela').css('color', 'red');
			}

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
			// Focus
			$('#' + divId + ' #' + request.data['campoFocus']).focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
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
function carregaCFOP_Contatos(elem, divId, T005_C004_Id_Estoque, alterarVendedorAusente, alterarVendedor) {
    $('#' + divId).showLoading();
    setTimeout(function() {
	    $.getJSON('/ven/ven002/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T005_D024_Id=' + encodeURIComponent($('#T005_D024_Id').val()) + '&T005_C004_Id_Estoque=' + encodeURIComponent(T005_C004_Id_Estoque) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#T005_D006_Id').html(request.data['T005_D006_Id']);
				$('#T005_C007_Id_Vendedor_Interno').val(request.data['T005_C007_Id_Vendedor_Interno']);
				$('#T005_C007_Id_Vendedor_Externo').val(request.data['T005_C007_Id_Vendedor_Externo']);
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

				reloadUI('#' . divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    			dialogConfirm("Erro!", request.data, $buttons)
			}
		});
	}, 100);
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
	
} </script>
</div>

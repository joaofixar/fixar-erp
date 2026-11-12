<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {


function campoProcuraProduto(divId, D009_Id, T007_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven003/form_func-ajax/atualizarCamposProdutos/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#T008_Codigo_Substituto').val(request.data['T008_Codigo_Substituto']);
			$('#Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#Procura_Produto').val(request.data['D001_Codigo_Produto']);      
			$('#D082_Marca').val(request.data['D082_Marca']);
			$('#T008_D037_Id').val(request.data['D001_D037_Id']);
			$('#T008_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#T008_D009_Id').val(request.data['D009_Id']);
			$('#T008_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#Pesquisa_Grid_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#T008_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#CampoTeste').val(request.data['Teste']);
			$('#Marca').val(request.data['D082_Marca']);
			$('#T008_D005_Id').val(request.data['D005_Id']);
			$('#T008_D006_Id').val(request.data['D006_Id']);			

			// ****** Valores completados *******
			$('#T008_Situacao_Tributaria_IPI').val(request.data['Situacao_Tributaria_IPI']);
			$('#T008_Valor_Preco_Sem_Desconto_Unitario').val(request.data['D041_Valor_Base']);
			$('#T008_Aliquota_PIS').val(request.data['C004_Percentual_PIS']);
			$('#T008_Aliquota_COFINS').val(request.data['C004_Percentual_COFINS']);
			$('#T008_Situacao_Tributaria_COFINS').val(request.data['D006_Situacao_Tributaria_COFINS']);
			$('#T008_Situacao_Tributaria_PIS').val(request.data['D006_Situacao_Tributaria_PIS']);
			$('#T008_D059_Id').val(request.data['D059_Id']);
			$('#T008_ST_ICMS_Interno').val(request.data['ST_ICMS_Interno']);
			$('#T008_ICMS_Inter_Estadual').val(request.data['T008_ST_ICMS_Inter_Estadual']);
			$('#T008_ST_VA').val(request.data['T008_ST_VA']);
			$('#T008_Flag_Isento_PIS').val(request.data['T008_Flag_Isento_PIS']);
			$('#T008_Flag_Isento_COFINS').val(request.data['T008_Flag_Isento_COFINS']);
			$('#T008_IPV').val(request.data['IPV']);
			$('#T008_Peso_Unitario').val(request.data['D001_Peso_Unitario_Kg']);
			$('#T008_Peso_Bruto').val(request.data['D001_Peso_Unitario_Bruto']);
			$('#T008_Percentual_Trib_Transparencia').val(request.data['T008_Percentual_Trib_Transparencia']);

			var D059_Id = request.data['D059_Id'];
			var id = $('#T008_D059_Id').closest('.ui-tabs-panel').attr('id');
			divSetarAcaoId(id, D059_Id, 'D059_Id');
			divRefresh(id, false, function() {
				$('#Codigo_Origem_Mercadoria').val(request.data['Origem_Mercadoria']);
				$('#T008_Aliquota_ICMS').val(request.data['Aliquota_ICMS']);
				$('#T008_Percentual_Reducao_ICMS').val(request.data['Percentual_Reducao_ICMS']);
			});
			
			// Focus
			$('#T008_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraProdutoEvento112140(divId, T008_Id, T321_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven003/form_func-ajax/atualizarCamposItemEvento112140/?ajax=true&T321_Id=' + encodeURIComponent(T321_Id) + '&T008_Id=' + encodeURIComponent(T008_Id) + '&callback=?', function(request) {
		if (request.code) {
			var formatoBrasileiro = function(valor, casas) {
				var numero = parseFloat(String(valor || 0).replace(',', '.'));
				return isNaN(numero) ? '' : numero.toLocaleString('pt-BR', { minimumFractionDigits: casas, maximumFractionDigits: casas });
			};
			$('#' + divId + ' #T322_T008_Id_Origem').val(request.data['T008_Id']);
			$('#' + divId + ' #Produto_Origem_Pesquisa').val(request.data['T008_Codigo_Produto'] + ' - ' + request.data['T008_Descricao_Produto']);
			$('#' + divId + ' #Quantidade_Original').val(formatoBrasileiro(request.data['T008_Quantidade'], 4));
			$('#' + divId + ' #Quantidade_Saldo').val(formatoBrasileiro(request.data['Quantidade_Saldo'], 4));
			$('#' + divId + ' #T322_Quantidade_Nao_Fornecida').val(formatoBrasileiro(request.data['Quantidade_Saldo'], 4));
			$('#' + divId + ' #Valor_IBS_Saldo').val(formatoBrasileiro(request.data['Valor_IBS_Saldo'], 2));
			$('#' + divId + ' #Valor_CBS_Saldo').val(formatoBrasileiro(request.data['Valor_CBS_Saldo'], 2));
			var $formEvento = $('#' + divId);
			var valorNumeroBrasileiro = function(valor) {
				return parseFloat(String(valor || 0).replace(/\./g, '').replace(',', '.')) || 0;
			};
			var recalcularTributosEvento = function() {
				var quantidadeSaldo = parseFloat(request.data['Quantidade_Saldo']) || 0;
				var quantidadeInformada = valorNumeroBrasileiro($formEvento.find('#T322_Quantidade_Nao_Fornecida').val());
				var fator = quantidadeSaldo > 0 ? quantidadeInformada / quantidadeSaldo : 0;
				$formEvento.find('#T322_Valor_IBS').val(formatoBrasileiro((parseFloat(request.data['Valor_IBS_Saldo']) || 0) * fator, 2));
				$formEvento.find('#T322_Valor_CBS').val(formatoBrasileiro((parseFloat(request.data['Valor_CBS_Saldo']) || 0) * fator, 2));
			};
			$formEvento.find('#T322_Quantidade_Nao_Fornecida').unbind('input.evento112140 change.evento112140').bind('input.evento112140 change.evento112140', recalcularTributosEvento);
			recalcularTributosEvento();
			$('#' + divId + ' #T322_Quantidade_Nao_Fornecida').focus();
		} else {
			dialogConfirm('Erro', request.data, { "Fechar": function() { $(this).dialog("destroy").remove(); } });
		}
		$('#' + divId).hideLoading();
	});
}


function carregaCFOP(elem, divId) {    
	if (!isempty($('#T007_D024_Id').val()) && $('#T007_D024_Id').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/ven/ven003/form_func-ajax/carregaCFOP/?ajax=true&T007_D024_Id=' + encodeURIComponent($('#T007_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#T007_D006_Id').html(request.data['T007_D006_Id']);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data['erro'], $buttons)
				}
			});
		}, 100);
	}
}

function carregaDestinoProduto(elem, divId) {    
	if (!isempty($('#T007_D024_Id').val()) && $('#T007_D024_Id').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/ven/ven003/form_func-ajax/carregaDestinoProduto/?ajax=true&T007_D024_Id=' + encodeURIComponent($('#T007_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#T007_Flag_Destino_Produto').val(request.data['T007_Flag_Destino_Produto']);
					if ($('#T007_D021_Id').size() > 0) { $('#T007_D021_Id').html(request.data['T007_D021_Id']); }
					$('#T007_Percentual_Comissao_Representante').val(request.data['T007_Percentual_Comissao_Representante']);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data['erro'], $buttons)
				}
			});
		}, 100);
	}
}

function campoProcuraServico(divId, D112_Id) {
    //divSetarAcaoId(divId, D009_Id, 'D009_Id');
    $('#' + divId).showLoading();
    $.getJSON('/ven/ven001/form_func-ajax/atualizarCamposServicos/?ajax=true&D112_Id=' + encodeURIComponent(D112_Id) + '&callback=?', function(request) {
        if (request.code) {
            // Preenche os campos
            //$('#' + divId + ' #T008_Codigo_Substituto').val(request.data['T008_Codigo_Substituto']);
            $('#' + divId + ' #Procura_Servico_Pesquisa').val(request.data['D112_Id']);
            $('#' + divId + ' #T203_Titulo').val(request.data['D112_Titulo']);
            $('#' + divId + ' #T203_Descricao').val(request.data['D112_Descricao']);
            $('#' + divId + ' #T203_Tempo_Estimado').val(request.data['D112_Tempo_Estimado']);
            $('#' + divId + ' #T203_Valor_Unitario').val(request.data['D112_Valor_Unitario']);
            $('#' + divId + ' #T203_Valor_Total').val(request.data['D112_Valor_Unitario']);
            $('#' + divId + ' #T203_D110_Id').val(request.data['D112_D110_Id']);
            $('#' + divId + ' #T203_D112_Id').val(request.data['D112_Id']);
            
            // Focus
            $('#' + divId + ' #T203_Quantidade').focus();
        } else {
            var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
            dialogConfirm('Erro', request.data, $buttons);
        }
        $('#' + divId).hideLoading();
    });
}

function ven003ProcuraCliente(divId, D024_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven003/form_func-ajax/procuraCliente/?ajax=true&D024_Id=' + D024_Id + '&callback=?', function(request) {
		if(request.code) {
			$('#' + divId).hideLoading();
			$('#' + divId + ' #D024_Id').val(request.data['D024_Id']);
			$('#' + divId + ' #Nome_Empresa_Pesquisa').val(request.data['D024_Nome_Fantasia']);
		}
	});
}

function campoProcuraProdutoConsumidor(divId, D009_Id, D144_Id, T007_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven003/form_func-ajax/atualizarCamposProdutosConsumidor/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T008_Codigo_Substituto').val(request.data['T008_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T008_D037_Id').val(request.data['D001_D037_Id']);
			$('#' + divId + ' #T008_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #T008_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T008_Numero_Pedido_Compra').val(request.data['T008_Numero_Pedido_Compra']);
			$('#' + divId + ' #T008_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #T008_D006_Id').val(request.data['D006_Id']);
			$('#' + divId + ' #T008_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T008_Flag_Destino_Produto').val(request.data['T007_Flag_Destino_Produto']);
			$('#' + divId + ' #T008_Percentual_Desconto').val(request.data['T008_Percentual_Desconto']);
			$('#' + divId + ' #D001_Id').val(request.data['D001_Id']);
			
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			//$('#' + divId + ' #NcmProduto').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #T008_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);
			$('#' + divId + ' #T008_Observacao_Entrega').val(request.data['T007_Prazo_Entrega']);
			
			$('#' + divId + ' #T008_Valor_Preco_Sem_Desconto_Unitario').val(request.data['Preco_Produto']);
			$('#' + divId + ' #PrecoMinimo').val(request.data['PrecoMinimo']);
			$('#' + divId + ' #T008_IPV').val(request.data['IPV']);
			//$('#' + divId + ' #T008_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			$('#' + divId + ' #T008_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId + ' #T008_D006_Id').val(request.data['D006_Id']);
			$('#' + divId + ' #Codigo_Origem_Mercadoria').val(request.data['D049_Origem_Mercadoria']);
			$('#' + divId + ' #UltimoPrecoVenda').val(request.data['ultimoPrecoVenda']);
			$('#' + divId + ' #T008_D144_Id').val(D144_Id);
			
			$('#' + divId + ' #estoqueLiquido').val(request.data['estoqueLiquido']);
			$('#' + divId + ' #estoqueLoja').val(request.data['estoqueLoja']);
			$('#' + divId + ' #estoqueTotal').val(request.data['estoqueTotal']);
			$('#' + divId + ' #ordemCompra').val(request.data['ordemCompra']);
			$('#' + divId + ' #estoqueFora').val(request.data['estoqueFora']);
			$('#' + divId + ' #precoTabela').val(request.data['precoTabela']);
			$('#' + divId + ' #precoTabela').css('color', 'black');
			if(request.data['validadePrecoTabela'] != 'true'){
				$('#' + divId + ' #precoTabela').css('color', 'red');
			}

			$('#' + divId + ' #estoqueLiquido').css('color', 'black');
			if(request.data['estoqueLiquido2'] <= 0){
				$('#' + divId + ' #estoqueLiquido').css('color', 'red');
			}
			$('.formEditor-infoCustoVenda').html(request.data['infoCustoVenda']);

			if(request.data['D001_Codigo_Produto']=='55555'){
				$('#' + divId + ' #T008_Descricao_Produto').removeAttr('readonly');
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

// Função criada para realizar o download do XML da nota, quando está não tiver sido autorizado pela SEFAZ.
function downloadXMLNaoAutorizado(T007_Id){
	var $buttons = { 
		"Confirmar": function(){
			window.open('/ven/ven003/form_func-ajax/downloadXMLNaoAutorizado/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id));
			$(this).dialog("destroy").remove(); 
		},
		"Fechar": function() { 
			$(this).dialog("destroy").remove(); 
		} 
	}
	dialogConfirm('Download XML', "<b><font class=fonte style='color: var(--BGVermelho);'>ATENÇÃO!!!</font></b><br/><br/>O XML  da nota <b><font class=fonte style='color: var(--BGVermelho);'>não está autorizado </font></b> pela SEFAZ. <br/><br/>Tem certeza que deseja baixar este XML?", $buttons);

}


} </script></div>

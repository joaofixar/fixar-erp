<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraProduto(divId, D009_Id, D144_Id, T218_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	//console.log(divId + ' - ' + D009_Id + ' - ' + T218_Id);
	$('#' + divId).showLoading();
	$.getJSON('/est/est015/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T218_Id='+ encodeURIComponent(T218_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T225_Codigo_Substituto').val(request.data['T225_Codigo_Substituto']);
			if(request.data['Validacao_NCM']!=""){
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['Validacao_NCM'], buttons)
			}
			/**
			 * PARA FUNCIONAR ESSE PESQUISA GRID BASTA ADICIONAR O PREFIXO "itemForm-" NO LUGAR DE "#"  
			 * $('#' + divId + ' itemForm-Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			 */
			
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			//console.log('#' + divId + ' #Procura_Produto_Pesquisa');
			$('#' + divId + ' #T225_D037_Id').val(request.data['D001_D037_Id']);
			$('#' + divId + ' #T225_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #T225_D006_Id').val(request.data['D006_Id']);
			$('#' + divId + ' #T225_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T225_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T225_D009_Id').val(request.data['D009_Id']);
			
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			//$('#' + divId + ' #NcmProduto').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #Marca_disabled').val(request.data['D082_Marca']);
			$('#' + divId + ' #T225_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);	
			$('#' + divId + ' #T225_Valor_Preco_Sem_Desconto_Unitario').val(request.data['T225_Valor_Preco_Sem_Desconto_Unitario']);
			//$('#' + divId + ' #T225_IPV').val(request.data['IPV']);
			//$('#' + divId + ' #T225_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			$('#' + divId + ' #T225_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId + ' #UltimoPrecoestda').val(request.data['ultimoPrecoestda']);
			$('#' + divId + ' #T225_D144_Id').val(D144_Id);
			// Focus
			$('#' + divId + ' #T225_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraProdutoEditar(divId, D009_Id, T218_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/est/est015/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T218_Id='+ encodeURIComponent(T218_Id) + '&callback=?', function(request) {
		if (request.code) {
			if(request.data['Validacao_NCM']!=""){
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['Validacao_NCM'], buttons)
			}
			$('#' + divId + ' #Procura_Produto_Edicao_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' .itemForm-T225_D037_Id').val(request.data['D001_D037_Id']);
			var divIdIPI = $('#' + divId).parent().find('.ui-tabs-panel:eq(1)').attr('id');
			$('#' + divIdIPI + ' .itemForm-T225_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' .itemForm-T225_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' .itemForm-T225_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' .itemForm-T225_D009_Id').val(request.data['D009_Id']);			
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			$('#' + divId + ' #Marca_disabled').val(request.data['D082_Marca']);
			$('#' + divId + ' .itemForm-T225_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);	
			$('#' + divId + ' .itemForm-T225_Valor_Preco_Sem_Desconto_Unitario').val(request.data['T225_Valor_Preco_Sem_Desconto_Unitario']);
			$('#' + divId + ' .itemForm-T225_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			$('#' + divId + ' #UltimoPrecoestda').val(request.data['ultimoPrecoestda']);
			$('#' + divId + ' .itemForm-T225_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function est015CampoProcuraProdutoTroca(divId, D009_Id, T218_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est015/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T218_Id='+ encodeURIComponent(T218_Id) + '&callback=?', function(request) {
		console.log(divId);
		if (request.code) {
			if(request.data['Validacao_NCM']!=""){
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['Validacao_NCM'], buttons)
			}
			// Preenche os campos
			$('#' + divId + ' #Procura_Produto_Troca_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T222_D009_Id').val(request.data['D009_Id']);						
			$('#' + divId + ' #Marca_Produto').val(request.data['D082_Marca']);	
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);		
			$('#' + divId + ' #T222_Valor_Preco_Unitario').val(request.data['D041_Valor_Base']);
			// Focus
			$('#' + divId + ' #T222_Quantidade').focus();
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
	$.getJSON('/est/est001/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T219_Codigo_Substituto').val(request.data['T219_Codigo_Substituto']);
			$('#' + divId + ' #est015FormMateriaisPesquisa_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T149_D037_Id').val(request.data['D001_D037_Id']); 
			//$('#' + divId + ' #T219_D005_Id').val(request.data['D001_D005_Id']);
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T149_D009_Id').val(request.data['D009_Id']);
			//$('#' + divId + ' #T219_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			
			$('#' + divId + ' #est015_Marca').val(request.data['D082_Marca']);
			//$('#' + divId + ' #NcmProduto').val(request.data['D001_D005_Id']);
			//$('#' + divId + ' #T219_Codigo_Substituto').val(request.data['D083_Codigo_Produto_Fornecedor']);			
			
			//$('#' + divId + ' #T219_Valor_Preco_Sem_Desconto_Unitario').val(request.data['D041_Valor_Base']);
			//$('#' + divId + ' #T219_IPV').val(request.data['IPV']);
			//$('#' + divId + ' #T219_Aliquota_IPI').val(request.data['D005_Aliquota_IPI']);
			//$('#' + divId + ' #T219_Aliquota_IPI').val(request.data['Aliquota_IPI']);
			
			// Focus
			$('#' + divId + ' #T230_Descricao').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function campoProcuraServicoPedido(divId, D112_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/est/est001/form_func-ajax/atualizarCamposServicos/?ajax=true&D112_Id=' + encodeURIComponent(D112_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T225_Codigo_Substituto').val(request.data['T225_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Servico_Pesquisa').val(request.data['D112_Titulo']);
			$('#' + divId + ' #T230_Titulo').val(request.data['D112_Titulo']);
			$('#' + divId + ' #T230_T212_Id').html(request.data['selectTipoServico']);
			// Focus
			$('#' + divId + ' #T230_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function carregaCFOP_Contatos(elem, divId) {
    $('#' + divId).showLoading();
    setTimeout(function() {
	    $.getJSON('/est/est015/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T218_D024_Id=' + encodeURIComponent($('#T218_D024_Id').val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#T218_D006_Id').html(request.data['T218_D006_Id']);
				$('#T218_D013_Id').html(request.data['T218_D013_Id']);
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
	    $.getJSON('/est/est015/form_func-ajax/carregaListaPreco/?ajax=true&T218_D024_Id=' + encodeURIComponent($('#T218_D024_Id').val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#T218_Flag_Lista_Preco').html(request.data['T218_Flag_Lista_Preco']);
				reloadUI('#' . divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    			dialogConfirm("Erro!", request.data, $buttons)
			}
		});
	}, 100);	
}
function gravarDescontoPedido(elem, divId, T218_Id, valor) {   
	//if (!isempty($('#T218_Valor_Desconto').val()) && $('#T218_Valor_Desconto').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/est/est015/form_func-ajax/gravarDescontoPedido/?ajax=true&T218_Valor_Desconto=' + encodeURIComponent($('#T218_Valor_Desconto').val()) + '&T218_Id=' + encodeURIComponent(T218_Id) + '&Valor=' + encodeURIComponent(valor) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					// $('#T218_D006_Id').html(request.data['T218_D006_Id']);
					// $('#T218_D013_Id').html(request.data['T218_D013_Id']); 
					// $('#T218_Flag_Reestda_Consumidor').val(request.data['T218_Flag_Reestda_Consumidor']);
					// $('#T218_C007_Id_estdedor_Interno').val(request.data['T218_C007_Id_estdedor_Interno']);
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
	$('#T218_D022_Id').val($('#TransportadoraPadrao_disabled').val());
    $('#T218_D022_Id_Pesquisa').val($("#TransportadoraPadrao_disabled option:selected").text());
    carregaTipoTransp();
}

function carregaTipoTransp(elem, divId) {
    setTimeout(function() {
        $.getJSON('/est/est015/form_func-ajax/carregaTipoTransp/?ajax=true&D022_Id=' + encodeURIComponent($('#T218_D022_Id').val()) + '&callback=?', function(request) {
            if (request.code) {
                $('#T218_D036_Id').val(request.data['T218_D036_Id']);
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
	$.getJSON('/est/est001/form_func-ajax/atualizarDadosServicos/?ajax=true&D112_Id=' + encodeURIComponent(D112_Id) + '&callback=?', function(request) {
		if (request.code) {
			//Preenche os campos
			$('#' + divId + ' #T230_Descricao').val(request.data['D112_Descricao']);
			$('#' + divId + ' #T230_Tempo_Estimado').val(request.data['D112_Tempo_Estimado']);
			$('#' + divId + ' #T230_Valor_Unitario').val(request.data['D112_Valor_Unitario']);
			$('#' + divId + ' #T230_Valor_Total').val(request.data['D112_Valor_Unitario']);
			$('#' + divId + ' #T230_D110_Id').val(request.data['D112_D110_Id']);
			$('#' + divId + ' #T230_D112_Id').val(request.data['D112_Id']);
			$('#' + divId + ' #T230_T212_Id').val(request.data['T212_Id']);
			$('#' + divId + ' #T230_Titulo').val(request.data['D112_Titulo']);
			$('#' + divId + ' #Procura_Tipo_Pesquisa').val(request.data['T212_Nome']);
			// Focus
			$('#' + divId + ' #T230_Descricao').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
	
} </script>
</div>

<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraProduto(divId, D009_Id, T075_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/imp/imp003/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T075_Id=' + encodeURIComponent(T075_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T006_Codigo_Substituto').val(request.data['T006_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #Marca').val(request.data['D082_Marca']);
			$('#' + divId + ' #T055_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T055_D009_Id').val(request.data['D009_Id']);
			$('#' + divId + ' #T055_Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T055_Descricao_Produto_Ingles').val(request.data['D001_Descricao_Ingles']);
			$('#' + divId + ' #T055_Peso_Kg_Liquido_Unitario').val(request.data['D001_Peso_Unitario_Kg']);
			$('#' + divId + ' #T055_D037_Id').val(request.data['D037_Id']);
			$('#' + divId + ' #T055_D005_Id').val(request.data['D005_Id']);
			$('#' + divId + ' #T055_Aliquota_PIS').val(request.data['D149_Importacao_PIS']);
			$('#' + divId + ' #T055_Aliquota_COFINS').val(request.data['D149_Importacao_COFINS']);
			$('#' + divId + ' #T055_Aliquota_Imposto_Importacao').val(request.data['D149_Imposto_Importacao']);
			$('#' + divId + ' #T055_Aliquota_IPI').val(request.data['D149_Importacao_IPI']);
			$('#' + divId + ' #T055_Aliquota_ICMS').val(request.data['D053_Aliquota_ICMS_Importacao']);
			$('#' + divId + ' #T055_Aliquota_ICMS_Presumido').val(request.data['D053_Aliquota_ICMS_Presumido_Importacao']);
			$('#' + divId + ' #T055_Percentual_Reducao_ICMS').val(request.data['D053_Percentual_Reducao_ICMS_Importacao']);
			$('#' + divId + ' #T055_Percentual_Reducao_ICMS_Presumido').val(request.data['D053_Percentual_Reducao_ICMS_Presumido_Importacao']);
			$('#' + divId + ' #T055_Aliquota_ICMS_PIS_COFINS').val(request.data['D053_Aliquota_ICMS_PIS_COFINS_Importacao']);
			$('#' + divId + ' #T055_Aliquota_ICMS_Credito_Presumido').val(request.data['D053_Aliquota_ICMS_Credito_Presumido_Importacao']);
			$('#' + divId + ' #T055A_Largura').val(request.data['D001A_Largura']);
			$('#' + divId + ' #T055A_Altura').val(request.data['D001A_Altura']);
			$('#' + divId + ' #T055A_Comprimento').val(request.data['D001A_Comprimento']);

			campoProcuraLocacao(D009_Id,divId);

			// Focus
			$('#' + divId + ' #T055_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function campoProcuraLocacao(D009_Id, divId) {

    setTimeout(function() {
	    $.getJSON('/imp/imp003/form_func-ajax/carregarLocacoes/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
			if (request.code) {
				// Locações
				if ($('#T055_T066_Id').size() > 0) { $('#T055_T066_Id').html(request.data['T055_T066_Id']); } 
				var T055_T066_Id_refreshBtn = $('#T055_T066_Id_refreshBtn').attr('onclick');
				var matches = T055_T066_Id_refreshBtn.match(/, '[^']+'/g);
				T055_T066_Id_refreshBtn = T055_T066_Id_refreshBtn.replace(matches[1], ", '" + request.data['T055_T066_Id_refreshBtn'] + "'");
				$('#T055_T066_Id_refreshBtn').attr('onclick', T055_T066_Id_refreshBtn);
				reloadUI('#' + divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['erro'], $buttons)
			}
		});
	}, 100);
}

function campoProcuraServico(divId, D112_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/ven/ven001/form_func-ajax/atualizarCamposServicos/?ajax=true&D112_Id=' + encodeURIComponent(D112_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T006_Codigo_Substituto').val(request.data['T006_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Servico_Pesquisa').val(request.data['D112_Id']);
			$('#' + divId + ' #T147_Titulo').val(request.data['D112_Titulo']);
			$('#' + divId + ' #T147_Descricao').val(request.data['D112_Descricao']);
			$('#' + divId + ' #T147_Tempo_Estimado').val(request.data['D112_Tempo_Estimado']);
			$('#' + divId + ' #T147_Valor_Unitario').val(request.data['D112_Valor_Unitario']);
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
function imp003CheckboxChecklist(checkbox,D122_Id,T075_Id,divId){
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	
	$('#' + divId).showLoading();
	$.getJSON('/imp/imp003/grid_func-ajax/gravarCampo/?ajax=true&D122_Id=' + encodeURIComponent(D122_Id) + '&T075_Id=' + encodeURIComponent(T075_Id) + '&selected=' + selected + '&callback=?', function(request) {
		//$(checkbox).attr('disabled', 'disabled').closest('td').next().find('span:eq(1)').html(request.data);
		if (selected=='S')
			$(checkbox).closest('td').next().find('span:eq(1)').html(request.data);
		$('#' + divId).hideLoading();		
	});
}

function alteraConta(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin002/form_func-ajax/alteraConta/?ajax=true&D032_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T015_D014_Id').html(request.data['T015_D014_Id']);
			reloadUI('#' . divId);
		} else {
			$('#T015_D014_Id').html(0);
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
	
} </script>
</div>

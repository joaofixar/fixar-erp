<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {

function est011CampoProcuraProduto(divId, D009_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est011/form_func-ajax/atualizarCamposProdutos/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		console.log(divId);
		if (request.code) {
			// Preenche os campos
			$('#' + divId + ' #Procura_Produto_Inclusao_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #T182_D009_Id').val(request.data['D009_Id']);						
			$('#' + divId + ' #Marca_Produto').val(request.data['D082_Marca']);	
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);		
			//$('#' + divId + ' #T138_Valor_Preco_Unitario').val(request.data['D041_Valor_Base']);
			// Focus
			$('#' + divId + ' #T182_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function preencheSemNota(){
	$('#T182_NF_Venda').val("NAO TEM");
}

function carregaContatoGarantiaCliente(elem, divId){
	$('#' + divId).showLoading();
	setTimeout(function() {
		$.getJSON('/est/est011/form_func-ajax/carregaContatoGarantiaCliente/?ajax=true&T182_D024_Id_Cliente=' + encodeURIComponent($('#T182_D024_Id_Cliente').val()) + '&callback=?', function(request) {
		    $('#' + divId).hideLoading();
			if (request.code) {
				if ($('#T182_D013_Id_Cliente').size() > 0) { 
					$('#T182_D013_Id_Cliente').html(request.data['T182_D013_Id_Cliente']); 
				}
			
				var T182_D013_Id_Cliente_refreshBtn = $('#T182_D013_Id_Cliente_refreshBtn').attr('onclick');
				var matches = T182_D013_Id_Cliente_refreshBtn.match(/, '[^']+'/g);
				T182_D013_Id_Cliente_refreshBtn = T182_D013_Id_Cliente_refreshBtn.replace(matches[1], ", '" + request.data['T182_D013_Id_Cliente_refreshBtn'] + "'");
				$('#T182_D013_Id_Cliente_refreshBtn').attr('onclick', T182_D013_Id_Cliente_refreshBtn);
				
				if ($('#T182_D024_Id_Cliente_Pesquisa').data('definirContato') > 0) {
					$('.itemForm-T182_D013_Id_Cliente').val($('#T182_D024_Id_Cliente_Pesquisa').data('definirContato'));
				}
            } else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['erro'], $buttons)
			}
		});
	}, 100);
}

function carregaContatoGarantiaFornecedor(elem, divId){
	$('#' + divId).showLoading();
	setTimeout(function() {
		$.getJSON('/est/est011/form_func-ajax/carregaContatoGarantiaFornecedor/?ajax=true&T182_D024_Id_Fornecedor=' + encodeURIComponent($('#T182_D024_Id_Fornecedor').val()) + '&callback=?', function(request) {
		    $('#' + divId).hideLoading();
			if (request.code) {
				if ($('#T182_D013_Id_Fornecedor').size() > 0) { 
					$('#T182_D013_Id_Fornecedor').html(request.data['T182_D013_Id_Fornecedor']); 
				}

				var T182_D013_Id_Fornecedor_refreshBtn = $('#T182_D013_Id_Fornecedor_refreshBtn').attr('onclick');
				var matches = T182_D013_Id_Fornecedor_refreshBtn.match(/, '[^']+'/g);
				T182_D013_Id_Fornecedor_refreshBtn = T182_D013_Id_Fornecedor_refreshBtn.replace(matches[1], ", '" + request.data['T182_D013_Id_Fornecedor_refreshBtn'] + "'");
				$('#T182_D013_Id_Fornecedor_refreshBtn').attr('onclick', T182_D013_Id_Fornecedor_refreshBtn);

				if ($('#T182_D024_Id_Fornecedor_Pesquisa').data('definirContato') > 0) {
					$('.itemForm-T182_D013_Id_Fornecedor').val($('#T182_D024_Id_Fornecedor_Pesquisa').data('definirContato'));
				}
            } else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				dialogConfirm("Erro!", request.data['erro'], $buttons)
			}
		});
	}, 100);
}

} </script>
</div>

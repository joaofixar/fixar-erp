<div class="divHidden">
	<script type="text/javascript"> if (typeof jQuery != 'undefined') {
function campoProcuraServico(divId, D112_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/srv/srv002/form_func-ajax/atualizarCamposServicos/?ajax=true&D112_Id=' + encodeURIComponent(D112_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T006_Codigo_Substituto').val(request.data['T006_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Servico_Pesquisa').val(request.data['D112_Id']);
			$('#' + divId + ' #T147_Titulo').val(request.data['D112_Titulo']);
			$('#' + divId + ' #T147_Descricao').val(request.data['D112_Descricao']);
			$('#' + divId + ' #T147_Tempo_Estimado').val(request.data['D112_Tempo_Estimado']);
			$('#' + divId + ' #T147_Valor_Unitario').val(request.data['D112_Valor_Unitario']);
			$('#' + divId + ' #T147_Valor_Total').val(request.data['D112_Valor_Unitario']);
			$('#' + divId + ' #T147_D110_Id').val(request.data['D112_D110_Id']);
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
function carregaVeiculo(elem, divId) {    
	if (!isempty($('#T147_D024_Id').val()) && $('#T147_D024_Id').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/srv/srv002/form_func-ajax/carregaVeiculo/?ajax=true&T147_D024_Id=' + encodeURIComponent($('#T147_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#T147_D116_Id').html(request.data['T147_D116_Id']);
					//$('#T003_D013_Id').html(request.data['T003_D013_Id']);
					reloadUI('#' . divId);
				} else {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data, $buttons)
				}
			});
		}, 100);
	}
}
	} </script>
</div>

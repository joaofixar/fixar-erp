<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraNCM(divId, T153_Id) {
	//divSetarAcaoId(divId, T153_Id, 'T153_Id');
	$('#' + divId).showLoading();
	$.getJSON('/fis/fis003/form_func-ajax/atualizarCamposNCM/?ajax=true&T153_Id=' + encodeURIComponent(T153_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId + ' #D005_Classificacao_Fiscal_Pesquisa').val(request.data['T153_NCM']);
			$('#' + divId + ' #D005_Classificacao_Fiscal').val(request.data['T153_NCM']);
			$('#' + divId + ' #D005_Observacao').val(request.data['T153_Descricao']);
			//$('#' + divId + ' #D005_Aliquota_IPI').val(request.data['T153_Aliquota']);
			// Focus
			$('#' + divId + ' #D005_Valor_VA').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
} </script></div>
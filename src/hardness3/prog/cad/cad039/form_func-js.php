<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraBem(divId, D106_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad039/form_func-ajax/atualizarCamposBens/?ajax=true&D106_Id=' + encodeURIComponent(D106_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T004_Codigo_Substituto').val(request.data['T004_Codigo_Substituto']);
			$('#' + divId + ' #Procura_Bem_Pesquisa').val(request.data['D106_Descricao']);
			$('#' + divId + ' #D124_D106_Id').val(request.data['D106_Id']);
			// Focus
			$('#' + divId + ' #D124_Observacao').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
} </script></div>

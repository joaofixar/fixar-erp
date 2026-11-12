<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function cad053CampoProcuraProduto(divId, D001_Id) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad052/form_func-ajax/atualizarCamposProdutos/?ajax=true&D001_Id=' + encodeURIComponent(D001_Id) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//var produto = request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto']; 
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #D074_D001_Id_Produto').val(request.data['D001_Id']);
			$('#' + divId + ' #D074_Quantidade').focus();
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

} </script></div>
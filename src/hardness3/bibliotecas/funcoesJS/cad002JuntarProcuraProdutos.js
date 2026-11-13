function cad002JuntarProcuraProdutos(divId, D001_Id, local) {
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad002/form_func-ajax/juntarProcuraProdutos/?ajax=true&D001_Id=' + D001_Id + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
		if(request.code) {
			$('#' + divId + ' .itemForm-D001_Id_' + local).val(request.data['D001_Id']);
			$('#' + divId + ' .itemForm-D001_Id_' + local + '_Pesquisa').val(request.data['D001_Descricao_Produto']);
		}
	});
}
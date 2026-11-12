function cad001JuntarProcuraClientes(divId, D024_Id, local) {
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad001/form_func-ajax/juntarProcuraClientes/?ajax=true&D024_Id=' + D024_Id + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
		if(request.code) {
			$('#' + divId + ' .itemForm-D024_Id_' + local).val(request.data['D024_Id']);
			$('#' + divId + ' .itemForm-D024_Id_' + local + '_Pesquisa').val(request.data['D024_Nome_Empresa']);
		}
	});
}
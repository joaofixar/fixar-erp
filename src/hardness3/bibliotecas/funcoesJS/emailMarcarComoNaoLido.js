function emailMarcarComoNaoLido(elem, T139_Id, divId) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/etc/etc006/grid_func-ajax/marcarComoNaoLido/?ajax=true&T139_Id=' + encodeURIComponent(T139_Id) + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
		if (request.code) {
			//
			$(elem).parent().html("<img src='/hardness3/static/img/tmp/icone_enviar.gif' />").parent().addClass('linhaFundoAmareloClaro');
		} else {
			//
		}
	});
}
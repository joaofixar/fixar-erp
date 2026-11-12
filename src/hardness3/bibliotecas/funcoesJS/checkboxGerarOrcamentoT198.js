function checkboxGerarOrcamentoT198(checkbox, D009_Id, T198_Id, divId){
	$('#' + divId).showLoading();
	$.getJSON('/crm/crm001/grid_func-ajax/crm001checkGerarOrcamento/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T198_Id=' + encodeURIComponent(T198_Id) + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
		if (request.code) {
			divRefresh(divId);
			/*if ($(checkbox).is(':checked')) {
		    		$(checkbox).attr('checked', false);
		   	} else {
		    		$(checkbox).attr('checked', true);
		    	}*/
		}
	});
}
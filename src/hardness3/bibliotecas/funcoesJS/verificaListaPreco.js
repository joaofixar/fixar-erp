function verificaListaPreco(elem, divId) {    
	if (!isempty($('#T003_D024_Id').val()) && $('#T003_D024_Id').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/ven/ven001/form_func-ajax/verificaFlagListaPreco/?ajax=true&T003_D024_Id=' + encodeURIComponent($('#T003_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (request.code) {
					$('#T003_Flag_Lista_Preco').html(request.data['T003_Flag_Lista_Preco']);
				} 
			});
		}, 100);
	}
}
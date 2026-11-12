<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function est005PopulaD073(divId, elem) {
	$('#' + divId).showLoading();
    $.getJSON('/est/est005/form_func-ajax/populaD073/?ajax=true&D006_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T084_D073_Id').val(request.data['D073_Id']);
		}
	});
}


} </script></div>
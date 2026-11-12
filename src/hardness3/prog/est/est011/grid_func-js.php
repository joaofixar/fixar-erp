<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function est011CheckboxMarcado(checkbox,T182_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	
	$('#' + divId).showLoading();				
	$.getJSON('/est/est011/grid_func-ajax/est011CheckboxMarcado/?ajax=true&T182_Id=' + encodeURIComponent(T182_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();					
	});	
}
} </script></div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)

function est010CheckboxCotar(checkbox,T006_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est010/grid_func-ajax/est010CheckboxCotar/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function est010CheckboxComprar(checkbox,T006_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est010/grid_func-ajax/est010CheckboxComprar/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
} </script></div>
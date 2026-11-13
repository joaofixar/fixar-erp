<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)

function est009CheckboxCotar(checkbox,D009_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est009/grid_func-ajax/est009CheckboxCotar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function est009CheckboxComprar(checkbox,D009_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est009/grid_func-ajax/est009CheckboxComprar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
} </script></div>
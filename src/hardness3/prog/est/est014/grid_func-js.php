<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)

function est014CheckboxCotar(checkbox,T189_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est014/grid_func-ajax/est014CheckboxCotar/?ajax=true&T189_Id=' + encodeURIComponent(T189_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function est014CheckboxFlagIndisponivel(checkbox,T174_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est014/grid_func-ajax/CheckboxFlagIndisponivel/?ajax=true&T174_Id=' + encodeURIComponent(T174_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}

function est014CheckboxGerarOC(checkbox,T174_Id,divId){
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est014/grid_func-ajax/CheckboxFlagGerarOC/?ajax=true&T174_Id=' + encodeURIComponent(T174_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function est014CheckboxComprar(checkbox,T189_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est014/grid_func-ajax/est014CheckboxComprar/?ajax=true&T189_Id=' + encodeURIComponent(T189_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function est014CheckboxProduzir(checkbox,T189_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est014/grid_func-ajax/est014CheckboxProduzir/?ajax=true&T189_Id=' + encodeURIComponent(T189_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
} </script></div>
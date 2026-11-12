<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)

function est019CheckboxCotar(checkbox,D009_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est019/grid_func-ajax/est019CheckboxCotar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function est019CheckboxComprar(checkbox,D009_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est019/grid_func-ajax/est019CheckboxComprar/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function est019CheckboxNaoComprar(checkbox,D049_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/est/est019/grid_func-ajax/est019CheckboxNaoComprar/?ajax=true&D049_Id=' + encodeURIComponent(D049_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}

function est019checkboxCotarEmergencia(checkbox,T004_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/est/est019/grid_func-ajax/checkboxCotarEmergencia/?ajax=true&T004_Id=' + encodeURIComponent(T004_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}

function est019checkboxCotarPedidoEmergencia(checkbox,T006_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/est/est019/grid_func-ajax/checkboxCotarPedidoEmergencia/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}

} </script></div>
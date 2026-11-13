<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function  selecionaAno(ano, divId){
	divSetarAcaoId(divId, ano, 'ano');
	divRefresh(divId, true);
}

function cad034CheckboxFlagConfirmado(checkbox,T018_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();				
	$.getJSON('/cad/cad034/grid_func-ajax/CheckboxFlagConfirmado/?ajax=true&T018_Id=' + encodeURIComponent(T018_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function cad034CheckboxFlagAtivo(checkbox,D014_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad034/grid_func-ajax/CheckboxFlagAtivo/?ajax=true&D014_Id=' + encodeURIComponent(D014_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}
function cad034CheckboxFlagAtivoGrupo(checkbox,D032_Id,divId) {
	var selected;
	if($(checkbox).is(':checked'))
		selected='S';
	else
		selected='N';
	$('#' + divId).showLoading();
	$.getJSON('/cad/cad034/grid_func-ajax/CheckboxFlagAtivoGrupo/?ajax=true&D032_Id=' + encodeURIComponent(D032_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();
	});	
}

} </script></div>
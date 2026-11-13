function validaCampoConciliado(checkbox,T019_Id,divId) {
	var selected;
	if($(checkbox).is(':checked')){
		selected='1';
	} else {
		selected='0';
	}
	$.getJSON('/fin/fin003/grid_func-ajax/atualizarCampoConciliado/?ajax=true&T019_Id=' + encodeURIComponent(T019_Id) + '&selected=' + selected + '&callback=?', function(request) {});	
}
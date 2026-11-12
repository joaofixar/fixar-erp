<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function cad032CheckboxAcessos(checkbox,C029_Id,C021_Id,divId){
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
	   
		$('#' + divId).showLoading();
		$.getJSON('/cad/cad032/grid_func-ajax/gravarCampo/?ajax=true&C029_Id=' + encodeURIComponent(C029_Id) + '&C021_Id=' + encodeURIComponent(C021_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();		
		});
}

function cad032CheckboxResumosRelatorios(checkbox,C027_Id,C021_Id,divId){
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
	   
		$('#' + divId).showLoading();
		$.getJSON('/cad/cad032/grid_func-ajax/gravarCampoResumoselatorios/?ajax=true&C027_Id=' + encodeURIComponent(C027_Id) + '&C021_Id=' + encodeURIComponent(C021_Id) + '&selected=' + selected + '&callback=?', function(request) {
		$('#' + divId).hideLoading();		
		});
}
} </script></div>
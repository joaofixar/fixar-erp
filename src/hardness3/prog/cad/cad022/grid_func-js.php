<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)

	function cad022CheckboxFlagComercial(checkbox,D027_Id,divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
		//$('#' + divId).showLoading();				
		$.getJSON('/cad/cad022/grid_func-ajax/checkboxFlagComercial/?ajax=true&D027_Id=' + encodeURIComponent(D027_Id) + '&selected=' + selected + '&callback=?', function(request) {
			//$('#' + divId).hideLoading();
			if(request.code == true){
				divRefresh(divId);
			}
		});	
	}
	
} </script></div>
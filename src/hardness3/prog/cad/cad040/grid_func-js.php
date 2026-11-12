<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function cad040CheckboxChecklist(checkbox,D120_Id,D125_Id,divId,D106_Id){
			var selected;
			if($(checkbox).is(':checked'))
				selected='S';
			else
				selected='N';
		   
			$('#' + divId).showLoading();
			$.getJSON('/cad/cad040/grid_func-ajax/gravarCampo/?ajax=true&D120_Id=' + encodeURIComponent(D120_Id) + '&D106_Id=' + encodeURIComponent(D106_Id) +'&D125_Id=' + encodeURIComponent(D125_Id) + '&selected=' + selected + '&callback=?', function(request) {
			$('#' + divId).hideLoading();		
			});
	}
} </script></div>
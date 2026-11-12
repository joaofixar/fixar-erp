<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// -> campoExclusao($array)
	
	function fin009CheckboxFlagConfirmado(checkbox,T018_Id,divId) {
		var selected;
		if($(checkbox).is(':checked'))
			selected='S';
		else
			selected='N';
		$('#' + divId).showLoading();				
		$.getJSON('/fin/fin009/grid_func-ajax/CheckboxFlagConfirmado/?ajax=true&T018_Id=' + encodeURIComponent(T018_Id) + '&selected=' + selected + '&callback=?', function(request) {
			$('#' + divId).hideLoading();
		});	
	}

		
} </script></div>

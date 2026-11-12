<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function alteraEstado(elem, divId) {
	    $('#' + divId).showLoading();
	    $.getJSON('/cad/cad066/form_func-ajax/alteraEstado/?ajax=true&D172_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#D172_D020_Id').html(request.data['D172_D020_Id']);
				$('#D172_D020_Id').html(request.data['D172_D020_Id']);
				reloadUI('#' . divId);
			} else {
				var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
	    		dialogConfirm("Erro!", request.data, $buttons)
			}
		});
	}
} </script></div>
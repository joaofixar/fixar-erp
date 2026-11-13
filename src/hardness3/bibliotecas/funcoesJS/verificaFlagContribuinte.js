function verificaFlagContribuinte(elem, divId, divIdRoot, divIdParent) {    
	if (!isempty($('#T003_D024_Id').val()) && $('#T003_D024_Id').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/ven/ven001/form_func-ajax/verificaFlagContribuinte/?ajax=true&T003_D024_Id=' + encodeURIComponent($('#T003_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (!(request.code)) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data, $buttons);    	
    				//abrirJanela(false, divIdRoot, divIdParent, unique(), '', 'Editar Cadastro - ID: ' + $('#T003_D024_Id').val(), '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent($('#T003_D024_Id').val()), '[800,700]');			
				} 
			});
		}, 100);
	}
}
function verificaFlagContribuinteCotacao(elem, divId, divIdRoot, divIdParent) {    
	if (!isempty($('#T218_D024_Id').val()) && $('#T218_D024_Id').val() != 0) {
    	$('#' + divId).showLoading();
	    setTimeout(function() {
		    $.getJSON('/est/est015/form_func-ajax/verificaFlagContribuinte/?ajax=true&T218_D024_Id=' + encodeURIComponent($('#T218_D024_Id').val()) + '&callback=?', function(request) {
		        $('#' + divId).hideLoading();
				if (!(request.code)) {
					var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    				dialogConfirm("Erro!", request.data, $buttons);    	
    				//abrirJanela(false, divIdRoot, divIdParent, unique(), '', 'Editar Cadastro - ID: ' + $('#T218_D024_Id').val(), '/cad/cad015/content/cad015contentCadastro/', '&acaoId=' + encodeURIComponent($('#T218_D024_Id').val()), '[800,700]');			
				} 
			});
		}, 100);
	}
}
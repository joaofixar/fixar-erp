function atualizarComissaoRepresentante(elem, divId) {
    $.getJSON('/ven/ven003/form_func-ajax/atualizarComissaoRepresentante/?ajax=true&D021_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
		if(request.code){
			$('#' + divId + ' #T007_Percentual_Comissao_Representante').val(request.data);
		}
	});
}
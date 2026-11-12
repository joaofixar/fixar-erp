<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
function preencheCampos(elem, divId, valorTotal) {
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin005/form_func-ajax/preencheCampos/?ajax=true&T117_D007_Id=' + encodeURIComponent($(elem).val()) + '&valorTotal=' + encodeURIComponent(valorTotal) +  '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T117_Percentual_Multa').val(request.data['T117_Percentual_Multa']);
			$('#T117_Percentual_Juros_Dia').val(request.data['T117_Percentual_Juros_Dia']);
			$('#T117_Instrucoes').val(request.data['T117_Instrucoes']);
			$('#mensagemJurosMultas').val(request.data['mensagemJurosMultas']);			
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}
} </script></div>
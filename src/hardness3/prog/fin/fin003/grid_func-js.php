<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function carregaInputsTotais(T019_Id) {
	
	$.getJSON('/fin/fin003/grid_func-ajax/carregaInputsTotais/?ajax=true&T019_Id='+T019_Id+'&callback=?', function(request) {
		$('#inputLancamento').val(request.lancamento);
		$('#inputRecebimento').val(request.recebimento);
		$('#inputPagamento').val(request.pagamento);
		$('#inputResultado').val(request.resultado);
	});
}

function fin003MarcarRegistroExtrato(checkbox,T216_Id,divId) {
    var selected;
    if($(checkbox).is(':checked')) {
        selected='S';
    } else {
        selected='N';          
    }
    $.getJSON('/fin/fin003/grid_func-ajax/marcarRegistroExtrato/?ajax=true&T216_Id=' + encodeURIComponent(T216_Id) + '&selected=' + selected + '&callback=?', function(request) {});    
}
	
} </script></div>


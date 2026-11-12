<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

/**
* Utilizado exclussivamente no formulário 'Contas a Pagar' -> 'Reparcelar'
* Faz uma cópia do primeiro campo (linha) e modifica os seus dados
*/

function alteraConta(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin003/form_func-ajax/alteraConta/?ajax=true&D032_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T019_D014_Id').html(request.data['D014_Id']);
			reloadUI('#' . divId);
		} else {
			$('#T019_D014_Id').html(0);
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

function alteraContaTransacao(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin003/form_func-ajax/alteraConta/?ajax=true&D032_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T216_D014_Id').html(request.data['D014_Id']);
			reloadUI('#' . divId);
		} else {
			$('#T216_D014_Id').html(0);
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

function alteraContaRegra(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin003/form_func-ajax/alteraConta/?ajax=true&D032_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T217_D014_Id').html(request.data['D014_Id']);
			reloadUI('#' . divId);
		} else {
			$('#T217_D014_Id').html(0);
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

function retornaSaldoConta(divId) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	var D014_Id;
	var Data;
	D014_Id = $('#' + divId + ' #T019_D014_Id').val();	
	Data = $('#' + divId + ' #T019_Data_Lancamento').val();	
	$('#' + divId).showLoading();
	$.getJSON('/fin/fin003/form_func-ajax/fin003atualizarSaldoConta/?ajax=true&D014_Id=' + encodeURIComponent(D014_Id) + '&Data='+ encodeURIComponent(Data) + '&callback=?', function(request) {
		if (request.code) {
			// Preenche os campos
			//$('#' + divId + ' #T006_Codigo_Substituto').val(request.data['T006_Codigo_Substituto']);
			
			/**
			 * PARA FUNCIONAR ESSE PESQUISA GRID BASTA ADICIONAR O PREFIXO "itemForm-" NO LUGAR DE "#"  
			 * $('#' + divId + ' itemForm-Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			 */
			
			$('#' + divId + ' #Saldo_Conta').val(request.data);
			//console.log('#' + divId + ' #Procura_Produto_Pesquisa');

		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

} </script></div>
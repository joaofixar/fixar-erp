<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

/**
* Utilizado exclussivamente no formulário 'Contas a Pagar' -> 'Reparcelar'
* Faz uma cópia do primeiro campo (linha) e modifica os seus dados
*/

function alteraConta(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin002/form_func-ajax/alteraConta/?ajax=true&D032_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T015_D014_Id').html(request.data['T015_D014_Id']);
			reloadUI('#' . divId);
		} else {
			$('#T015_D014_Id').html(0);
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

function retornaSaldoConta(divId) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	var D014_Id;
	var D032_Id;
	var Data;
	D014_Id = $('#' + divId + ' #T015_D014_Id').val();	
	D032_Id = $('#' + divId + ' #T015_D032_Id').val();	
	Data = $('#' + divId + ' #T015_Data_Vencimento').val();	
	$('#' + divId).showLoading();
	$.getJSON('/fin/fin002/form_func-ajax/fin002atualizarSaldoConta/?ajax=true&D014_Id=' + encodeURIComponent(D014_Id) + '&D032_Id='+ encodeURIComponent(D032_Id) + '&Data='+ encodeURIComponent(Data) + '&callback=?', function(request) {
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

function calculaVencimento(barra) {
	if(barra.length > 0){
		var campo1 = barra.substr(0,4)+barra.substr(19,1)+'.'+barra.substr(20,4);
		var campo2 = barra.substr(24,5)+'.'+barra.substr(24+5,5);
		var campo3 = barra.substr(34,5)+'.'+barra.substr(34+5,5);
		var campo4 = barra.substr(4,1);		// Digito verificador
		var campo5 = barra.substr(5,14);	// Vencimento + Valor

		if(modulo11_banco(barra.substr(0,4)+barra.substr(5,99)) != campo4) {
			//Coleta do valor do documento
			var valorDuplicata, dia, mes, ano, dataVencimento, anoAtual;
			valorDuplicata = barra.substr(4,9)+'.'+barra.substr(13,2);
			valorDuplicata = number_format(valorDuplicata, 2, ',', '');
			$('#T015_Valor_Duplicata').val(valorDuplicata);
			//Coleta do vencimento do documento
			ano = barra.substr(19,4);	 mes = barra.substr(23,2);		dia = barra.substr(25,2);
			anoAtual = new Date();
			if((dia > 0 && dia <= 31) && (mes > 0 && mes <= 12) && (ano >= anoAtual.getFullYear())){
				dataVencimento = dia+'/'+mes+'/'+ano;
				$('#T015_Data_Vencimento').val(dataVencimento);			
			}
		} else {
			if (barra.substr(5,4) == 0) {
				$('#T015_Data_Vencimento').val('');
			} else {
				$('#T015_Data_Vencimento').val(fator_vencimento(barra.substr(5,4)));
				$('#T015_Valor_Duplicata').val((barra.substr(9,8)*1)+','+barra.substr(17,2));
			}
		}
	}
}

function fator_vencimento(dias) {
	var currentDate, t, d, mes, ano;
	t = new Date();
	currentDate = new Date();
	currentDate.setFullYear(1997,9,7);
	t.setTime(currentDate.getTime() + (1000 * 60 * 60 * 24 * dias));
	mes = (t.getMonth()+1); if (mes < 10) mes = "0" + mes;
	dia = (t.getDate()); if (dia < 10) dia = "0" + dia;
	ano = t.getFullYear();
	return dia+'/'+mes+'/'+ano;
}

function modulo11_banco(numero) {
	numero = numero.replace(/[^0-9]/g,'');
	var soma  = 0;
	var peso  = 2;
	var base  = 9;
	var resto = 0;
	var contador = numero.length - 1;
	for (var i=contador; i >= 0; i--) {
		soma = soma + ( numero.substring(i,i+1) * peso);
		if (peso < base) {
			peso++;
		} else {
			peso = 2;
		}
	}
	var digito = 11 - (soma % 11);
	if (digito >  9) digito = 0;
	if (digito == 0) digito = 1;
	return digito;
}

} </script></div>
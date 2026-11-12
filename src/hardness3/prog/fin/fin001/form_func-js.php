<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

/**
* Utilizado exclussivamente no formulário 'Contas a Pagar' -> 'Reparcelar'
* Faz uma cópia do primeiro campo (linha) e modifica os seus dados
*/
function fin001AdicionarParcela(divId) {
	var $tr = $('#'+divId+' #valor1').closest('tr');
	var $clone = $tr.clone();
	var size = $tr.parent().find('tr').size()-1;
	$clone.find('#vencimento1').attr('id', 'vencimento' + size).attr('name', 'form_fin001_reparcelar[vencimento' + size + ']');
	$clone.find('#valor1').attr('id', 'valor' + size).attr('name', 'form_fin001_reparcelar[valor' + size + ']');
	$clone.find('span:eq(0)').html('Valor ' + size);
	if (size > 9){
		$clone.find('span:eq(2)').html('Vencimento ' + size + '&nbsp;');
	}else{
		$clone.find('span:eq(2)').html('Vencimento ' + size + '&nbsp;&nbsp;&nbsp;');
	}
	$clone.find('input').removeClass('hasDatepicker').val('');
	$clone.insertBefore($tr.parent().find('tr:last'));
	if (size > 23) {
		$('#'+divId+' .ui-icon-plusthick').closest('td').remove();
	}
	fin001FixBotaoX(divId);
	reloadUI(divId);
}

function alteraConta(elem, divId) {
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin001/form_func-ajax/alteraConta/?ajax=true&D032_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
		if (request.code) {
			$('#T002_D014_Id').html(request.data['T002_D014_Id']);
			reloadUI('#' . divId);
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		dialogConfirm("Erro!", request.data, $buttons)
		}
	});
}

/**
* Utilizado exclussivamente no formulário 'Contas a Pagar' -> 'Reparcelar'
* Corrige os números das parcelas após a remoção de uma (caso excluído no meio)
*/
function fin001FixParcelas(divId) {
	var $tbody = $('#'+divId+' #valor1').closest('tr').parent();
	var size = $tbody.find('tr').size()-1;
	$tbody.find('tr').each(function(index) {
		if (size != index && index > 1) {
			var $clone = $(this);
			$clone.find('#vencimento1').attr('id', 'vencimento' + index).attr('name', 'form_fin001_reparcelar[vencimento' + index + ']');
			$clone.find('#valor1').attr('id', 'valor' + index).attr('name', 'form_fin001_reparcelar[valor' + index + ']');
			$clone.find('span:eq(0)').html('Valor ' + index);
			if (size > 9){
				$clone.find('span:eq(2)').html('Vencimento ' + index + '&nbsp;');
			} else  {
				$clone.find('span:eq(2)').html('Vencimento ' + index + '&nbsp;&nbsp;&nbsp;');
			}
		}
	});
}

/**
* Utilizado exclussivamente no formulário 'Contas a Pagar' -> 'Reparcelar'
* Adiciona o botão de remover (X) nas parcelas dinamicas
*/
function fin001FixBotaoX(divId) {
	var $tbody = $('#'+divId+' #valor1').closest('tr').parent();
	var size = $tbody.find('tr').size()-1;
	$tbody.find('tr').each(function(index) {
		if (size != index && index > 1) {
			if ($(this).find('.ui-icon-closethick').size() == 0) {
				$('<a href="#"><span title="Remover Parcela" class="ui-icon ui-icon-closethick">Remover</span></a>')
				.bind('click', function() {  
					$(this).closest('tr').remove();
					fin001FixParcelas(divId);
				})
				.appendTo($(this));
			}
		}
	});
}

function calculaJuros(elem, divId, acaoId, type) {
    $('#' + divId).showLoading();
    $.getJSON('/fin/fin001/form_func-ajax/calculaJuros/?ajax=true&novaData=' + encodeURIComponent($(elem).val()) + '&acaoId=' + encodeURIComponent(acaoId) + '&callback=?', function(request) {
        $('#' + divId).hideLoading();
        if (request.code) {
            $('#DiasProrrogados').val(request.data['diasProrrogados']);
            $('#JurosCalculado').val(request.data['valorJuros']);
            reloadUI('#' . divId);
        } else {
            var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
            dialogConfirm("Erro!", request.data, $buttons)
        }
    });
}

function calcularValorTroco(divId, extra) {
	var valorRecebido = parseFloat($('#' + divId + ' #ValorRecebido').val());

	$('#' + divId + ' #ValorTotal').val(0);
	$('#' + divId + ' #ValorTroco').val(0);
	$('#' + divId + ' #ValorTroco').css("color", "black");
    $.getJSON('/fin/fin001/form_func-ajax/calcularValorTroco/?ajax=true&extra=' + encodeURIComponent(extra) + '&valorRecebido=' + encodeURIComponent(valorRecebido) + '&callback=?', function(request) {
        if (request.code) {
            $('#' + divId + ' #ValorTotal').val(request.data['ValorTotal']);
            $('#' + divId + ' #ValorTroco').val(request.data['ValorTroco']);
			
			if (parseFloat(request.data['ValorTroco']) < 0) {
				$('#' + divId + ' #ValorTroco').css("color", "red");
			} else {
				$('#' + divId + ' #ValorTroco').css("color", "green");
			}
        }
    });
}
} </script></div>
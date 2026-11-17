<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

function campoProcuraProdutoNota(divId, D009_Id, divReload, acaoId) {
	//divSetarAcaoId(divId, D009_Id, 'D009_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est004/form_func-ajax/atualizarCamposProdutosNota/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&T013_Id=' + encodeURIComponent(acaoId) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Pesquisa').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #Descricao_Produto').val(request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #T014_D009_Id').val(request.data['D009_Id']);		
			$('#' + divId + ' #T014_D006_Id').html(request.data['D006_Id']);		
			
			//Locação
			$('#' + divId + ' #T014_T066_Id').html(request.data['T014_T066_Id']);
			var T014_T066_Id_refreshBtn = $('#' + divId + ' #T014_T066_Id_refreshBtn').attr('onclick');
			var matches = T014_T066_Id_refreshBtn.match(/, '[^']+'/g);
			T014_T066_Id_refreshBtn = T014_T066_Id_refreshBtn.replace(matches[1], ", '" + request.data['T014_T066_Id_refreshBtn'] + "'");
			$('#' + divId + ' #T014_T066_Id_refreshBtn').attr('onclick', T014_T066_Id_refreshBtn);			

			divSetarAcaoId(divId, D009_Id, 'idD009');
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function campoProcuraProdutoAssociar(divId, T008_Id, T014_Id) {
	//divSetarAcaoId(divId, T008_Id, 'T008_Id');
	$('#' + divId).showLoading();
	$.getJSON('/est/est004/form_func-ajax/atualizarCamposProdutosAssociar/?ajax=true&T008_Id=' + encodeURIComponent(T008_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Associar_Pesquisa').val(request.data['T008_Codigo_Produto'] + ' - ' + request.data['T008_Descricao_Produto']);
			$('#' + divId + ' #T008_Codigo_Produto').val(request.data['T008_Codigo_Produto']);
			$('#' + divId + ' #D009_Id').val(request.data['T008_D009_Id']);
			$('#' + divId + ' #T008_Id').val(request.data['T008_Id']);			
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}
function campoProcuraProdutoAssociarItem(divId, D009_Id) {
	$('#' + divId).showLoading();
	$.getJSON('/est/est004/form_func-ajax/campoProcuraProdutoAssociarItem/?ajax=true&D009_Id=' + encodeURIComponent(D009_Id) + '&callback=?', function(request) {
		if (request.code) {
			$('#' + divId + ' #Procura_Produto_Associar_Pesquisa').val(request.data['D001_Codigo_Produto'] + ' - ' + request.data['D001_Descricao_Produto']);
			$('#' + divId + ' #D001_Codigo_Produto').val(request.data['D001_Codigo_Produto']);
			$('#' + divId + ' #D009_Id').val(request.data['D009_Id']);			
		} else {
			var $buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
    		dialogConfirm('Erro', request.data, $buttons);
		}
		$('#' + divId).hideLoading();
	});
}

function preencheNaoTem(){
	$('#T013_Numero_Conhecimento').val("NAO TEM");
}

function est004AlternarFreteNFS(campo) {
    var area = $(campo).closest('.formFormatoEditor');
    area.find('.formEditor-Frete_NFS_Nota_Id, .formEditor-est004PesquisarFreteNFS, .formEditor-est004RatearFreteNFS').toggle($(campo).val() === '4');
}

function est004PesquisarFreteNFS(botao) {
    var campo = $(botao).closest('.formFormatoEditor').find('#Frete_NFS_Nota_Id_Pesquisa');
    campo.focus().trigger($.Event('keypress', { keyCode: 13, which: 13 }));
}

function est004SelecionarNotaFreteNFS(divId, notaId, numeroNota) {
    var area = $('#' + divId);
    area.find('#Frete_NFS_Nota_Id').val(notaId);
    area.find('#Frete_NFS_Nota_Id_Pesquisa').val(numeroNota);
    area.find('#est004RatearFreteNFS').focus();
}

function est004LimparSelecaoFreteNFS(campo) {
    $(campo).closest('.formFormatoEditor').find('#Frete_NFS_Nota_Id').val('');
}

function est004RatearFreteNFS(botao, divId) {
    var $botao = $(botao);
    if ($botao.prop('disabled')) { return; }
    var area = $(botao).closest('.formFormatoEditor');
    var origemId = area.find('#Frete_NFS_Origem_Id').val();
    var destinoId = area.find('#Frete_NFS_Nota_Id').val();
    var valorSalvo = area.find('#Frete_NFS_Valor_Salvo').val();
    var valorDigitado = $(botao).closest('.ui-dialog-content').find('#T013_Valor_Total').first().val();
    function mensagem(titulo, texto) {
        dialogConfirm(titulo, texto, { 'Fechar': function() { $(this).dialog('destroy').remove(); } });
    }
    if (!origemId || origemId === '0') {
        mensagem('Frete NFS', 'Salve a NFS antes de ratear o frete.');
        return;
    }
    if (!destinoId || destinoId === '0') {
        mensagem('Frete NFS', 'Pesquise e selecione a nota de produtos.');
        return;
    }
    if (valorDigitado && Math.abs(Number(valorDigitado.replace(/\./g, '').replace(',', '.')) - Number(valorSalvo)) > 0.001) {
        mensagem('Frete NFS', 'Salve o valor total da NFS antes de ratear o frete.');
        return;
    }
    var rotuloOriginal = $botao.val();
    $botao.val('Rateando...').prop('disabled', true).attr('aria-busy', 'true');
    var $aba = $('#' + divId);
    $aba.showLoading();
    var progressoId = dialogAlert('Rateando frete NFS...',
        '<div role="status" aria-live="polite" style="min-width:320px;padding:12px;">' +
        '<img src="/hardness3/static/img/loading.gif" alt="" style="width:24px;height:24px;margin-right:10px;vertical-align:middle;">' +
        'Distribuindo o frete e atualizando o custo dos itens. Aguarde...</div>');
    $('#dc-' + progressoId).dialog('option', 'closeOnEscape', false);
    var encerrado = false;
    function finalizarCarregamento() {
        if (encerrado) { return; }
        encerrado = true;
        $('#dc-' + progressoId).dialog('destroy').remove();
        $aba.hideLoading();
        $botao.val(rotuloOriginal).prop('disabled', false).removeAttr('aria-busy');
    }
    try {
        $.ajax({
            type: 'POST',
            url: '/est/est004/form_func-ajax/ratearFreteNFS/?ajax=true',
            dataType: 'json',
            timeout: 120000,
            data: { origemId: origemId, destinoId: destinoId, forma: area.find('#T013_Flag_Rateio_Frete').val(), valorEsperado: valorSalvo },
            success: function(resposta) {
                finalizarCarregamento();
                if (!resposta || typeof resposta.code !== 'boolean') {
                    mensagem('Frete NFS', 'O servidor não confirmou o resultado. Reabra a aba Frete para conferir o vínculo antes de tentar novamente.');
                    return;
                }
                mensagem(resposta.code ? 'Rateio concluído' : 'Não foi possível ratear', resposta.data);
                if (resposta.code) { divRefresh(divId, true); }
            },
            error: function(xhr, estado) {
                finalizarCarregamento();
                var texto = estado === 'timeout'
                    ? 'O tempo de espera foi excedido e o rateio pode ainda estar em processamento. Reabra a aba Frete para conferir o vínculo antes de tentar novamente.'
                    : 'O servidor não confirmou o resultado do rateio. Reabra a aba Frete para conferir o vínculo antes de tentar novamente.';
                mensagem('Frete NFS', texto);
            },
            complete: finalizarCarregamento
        });
    } catch (erro) {
        finalizarCarregamento();
        mensagem('Frete NFS', 'Não foi possível iniciar a solicitação. Reabra a aba Frete e tente novamente.');
    }
}

} </script></div>

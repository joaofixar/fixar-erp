/* Configuração padrão compartilhada por todas as instâncias da calculadora. */
function configurarCalculadoraQuantidade(opcoes) {
	window.calculadoraQuantidadeConfiguracao = window.calculadoraQuantidadeConfiguracao || {};
	opcoes = opcoes || {};
	if (typeof opcoes.participarTab !== 'undefined') {
		window.calculadoraQuantidadeConfiguracao.participarTab = opcoes.participarTab !== false;
	}
}

function inicializarCalculadoraQuantidade(opcoes) {
	if (typeof jQuery === 'undefined') { return; }
	var $ = jQuery;
	var formulario = $(opcoes.formulario);
	var campo = formulario.find(opcoes.campo);
	if (!formulario.length || !campo.length) { return; }
	var configuracao = window.calculadoraQuantidadeConfiguracao || {};
	var participarTab = typeof opcoes.participarTab !== 'undefined'
		? opcoes.participarTab !== false
		: configuracao.participarTab !== false;

	var classe = 'calculadoraQuantidadeBtn';
	if (!campo.siblings('.' + classe).length) {
		campo.after('<span class="' + classe + '" role="button" tabindex="' + (participarTab ? '0' : '-1') + '" title="Calculadora de Quantidade" aria-label="Calculadora de Quantidade" style="display:inline-flex;align-items:center;justify-content:center;width:17px;height:17px;margin-left:4px;padding:0;border:0;background:transparent;box-shadow:none;line-height:0;vertical-align:middle;cursor:pointer;"><img src="/hardness3/static/img/icons/calculator.png" alt="" aria-hidden="true" style="display:block;width:17px;height:17px;"></span>');
	}

	var botao = campo.siblings('.' + classe).attr('tabindex', participarTab ? '0' : '-1');
	if (opcoes.antesDe) {
		var referencia = formulario.find(opcoes.antesDe);
		if (referencia.length) { botao.css('float', 'right').insertBefore(referencia); }
	}
	botao.unbind('click.calculadoraQuantidade').bind('click.calculadoraQuantidade', function(event) {
		var valor = function(seletor) { return seletor ? formulario.find(seletor).val() || '' : ''; };
		var parametros = '&formId=' + encodeURIComponent(opcoes.formId || formulario.attr('id'))
			+ '&evento=' + encodeURIComponent(opcoes.evento || 'calculadoraQuantidade:resultado')
			+ '&altura=' + encodeURIComponent(valor(opcoes.altura))
			+ '&largura=' + encodeURIComponent(valor(opcoes.largura))
			+ '&comprimento=' + encodeURIComponent(valor(opcoes.comprimento))
			+ '&resultado=' + encodeURIComponent(valor(opcoes.resultado));
		abrirJanela(event, opcoes.divIdRoot, opcoes.divIdParent, unique(), '', 'Calculadora de Quantidade', '/sistema/calculadoraQuantidade/content/calculadoraQuantidade/', parametros, opcoes.tamanho || [240, 230]);
		return false;
	}).unbind('keydown.calculadoraQuantidade').bind('keydown.calculadoraQuantidade', function(event) {
		var tecla = event.which || event.keyCode;
		if (tecla === 13 || tecla === 32) { event.preventDefault(); $(this).trigger('click'); return false; }
	});
}

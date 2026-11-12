<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'calculadoraQuantidade':
		$calculadoraId = 'calculadoraQuantidade' . uniqid();
		$formOrigemId = isset($_REQUEST['formId']) ? $_REQUEST['formId'] : '';
		$evento = isset($_REQUEST['evento']) && $_REQUEST['evento'] !== '' ? $_REQUEST['evento'] : 'calculadoraQuantidade:resultado';
		$medidas = array(
			'altura' => isset($_REQUEST['altura']) ? $_REQUEST['altura'] : 0,
			'largura' => isset($_REQUEST['largura']) ? $_REQUEST['largura'] : 0,
			'comprimento' => isset($_REQUEST['comprimento']) ? $_REQUEST['comprimento'] : 0,
			'resultado' => isset($_REQUEST['resultado']) ? $_REQUEST['resultado'] : 0,
		);
		$campoAltura = $calculadoraId . '_altura';
		$campoLargura = $calculadoraId . '_largura';
		$campoComprimento = $calculadoraId . '_comprimento';
		$campoResultado = $calculadoraId . '_resultado';
		$botaoUsar = $calculadoraId . '_usar';

		$form_tabela = '';
		$form_campo_pk = '';
		$form_editor = array('geral' => json_encode(array(
			$campoAltura => array('top' => 7, 'left' => 25, 'width' => 150, 'hidden' => 0),
			$campoLargura => array('top' => 30, 'left' => 15, 'width' => 150, 'hidden' => 0),
			$campoComprimento => array('top' => 53, 'left' => 10, 'width' => 150, 'hidden' => 0),
			$campoResultado => array('top' => 76, 'left' => 20, 'width' => 150, 'hidden' => 0),
			$botaoUsar => array('top' => 116, 'left' => 125, 'width' => null, 'hidden' => 0),
		)));
		$formArray = array('campos' => array('geral' => array(
			$campoAltura => array('name' => $campoAltura, 'label' => 'Alt', 'type' => 'moeda', 'casasDecimais' => 5, 'reject' => true, 'uppercase' => false),
			$campoLargura => array('name' => $campoLargura, 'label' => 'Larg', 'type' => 'moeda', 'casasDecimais' => 5, 'reject' => true, 'uppercase' => false),
			$campoComprimento => array('name' => $campoComprimento, 'label' => 'Comp', 'type' => 'moeda', 'casasDecimais' => 5, 'reject' => true, 'uppercase' => false),
			$campoResultado => array('name' => $campoResultado, 'label' => 'Res', 'type' => 'moeda', 'casasDecimais' => 5, 'readonly' => true, 'reject' => true, 'uppercase' => false),
			$botaoUsar => array('name' => $botaoUsar, 'label' => '', 'type' => 'button', 'reload' => false, 'value' => 'Usar Valor', 'reject' => true),
		)));
		$form = new Formulario($calculadoraId);
		$form->definirFormularioArray($formArray);
		$gerado = gGeraCamposForm($form, 'geral', false, false, '', $calculadoraId, $form_campo_pk, false, false, false, false);
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', $form_tabela);
		echo $gerado['pagina'];
		$g['smarty']->assign('formId', $calculadoraId);
		$g['smarty']->display('formEditor.tpl');

		$formOrigemIdJson = json_encode($formOrigemId);
		$eventoJson = json_encode($evento);
		$medidasJson = json_encode($medidas);
		echo <<<JS
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			(function($) {
				var calculadora = $('#{$calculadoraId}');
				var formOrigemId = {$formOrigemIdJson};
				var evento = {$eventoJson};
				var medidasIniciais = {$medidasJson};
				var campos = {
					altura: calculadora.find('#{$campoAltura}'),
					largura: calculadora.find('#{$campoLargura}'),
					comprimento: calculadora.find('#{$campoComprimento}'),
					resultado: calculadora.find('#{$campoResultado}')
				};
				function numero(valor) {
					valor = $.trim(String(valor || ''));
					if (!valor) { return NaN; }
					if (valor.indexOf(',') !== -1) { valor = valor.replace(/\./g, '').replace(',', '.'); }
					return Number(valor);
				}
				function formatar(valor) { return Number(valor).toFixed(5).replace('.', ','); }
				function obterMedidas() {
					var retorno = {altura: 0, largura: 0, comprimento: 0, valores: [], invalida: false};
					$.each([{nome: 'altura', campo: campos.altura}, {nome: 'largura', campo: campos.largura}, {nome: 'comprimento', campo: campos.comprimento}], function(_, medida) {
						var texto = $.trim(String(medida.campo.val() || ''));
						if (!texto) { return; }
						var valor = numero(texto);
						if (!isFinite(valor) || valor < 0) { retorno.invalida = true; return; }
						if (valor > 0) { retorno[medida.nome] = valor; retorno.valores.push(valor); }
					});
					return retorno;
				}
				function calcularResultado(medidas) {
					if (medidas.invalida || medidas.valores.length < 2) { return false; }
					var resultado = 1;
					$.each(medidas.valores, function(_, valor) { resultado *= valor; });
					return resultado;
				}
				function recalcular() {
					var resultado = calcularResultado(obterMedidas());
					campos.resultado.val(resultado !== false && resultado > 0 ? formatar(resultado) : '');
				}
				$.each([{nome: 'altura', campo: campos.altura}, {nome: 'largura', campo: campos.largura}, {nome: 'comprimento', campo: campos.comprimento}], function(_, medida) {
					var valor = numero(medidasIniciais[medida.nome]);
					medida.campo.val(isFinite(valor) && valor > 0 ? formatar(valor) : '');
				});
				recalcular();
				calculadora.find('input:not([readonly])').unbind('input.calculadoraQuantidade change.calculadoraQuantidade').bind('input.calculadoraQuantidade change.calculadoraQuantidade', recalcular);
				var botaoUsar = calculadora.find('#{$botaoUsar}');
				if (botaoUsar.is('input')) {
					var botaoPadrao = $('<button type="button"></button>').attr('id', botaoUsar.attr('id')).addClass(botaoUsar.attr('class')).text(botaoUsar.val());
					botaoUsar.replaceWith(botaoPadrao);
					botaoUsar = botaoPadrao;
				}
				botaoUsar.button().unbind('click.calculadoraQuantidade').bind('click.calculadoraQuantidade', function() {
					var medidas = obterMedidas();
					var resultado = calcularResultado(medidas);
					if (resultado === false || resultado <= 0) { alert('Informe pelo menos duas medidas numéricas maiores que zero.'); return; }
					var retorno = {altura: medidas.altura.toFixed(5), largura: medidas.largura.toFixed(5), comprimento: medidas.comprimento.toFixed(5), resultado: resultado.toFixed(5)};
					$('#' + formOrigemId).trigger(evento, [retorno]);
					$(document).trigger(evento, [retorno, formOrigemId]);
					$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
				});
			})(jQuery);
		} </script></div>
JS;
	break;
}

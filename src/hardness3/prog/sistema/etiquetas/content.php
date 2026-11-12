<?php
namespace hardness;


include ('prog/sistema/etiquetas/form_func-js.php');

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'sistemaEtiquetasSelecao':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/sistema/etiquetas/tab/sistemaEtiquetasSelecao/', '&D00X_Id=' . $r_D00X_Id)
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'sistemaEtiquetasQtdProduto':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/sistema/etiquetas/tab/sistemaEtiquetasQtdProduto/', '&D00X_Id=' . $r_D00X_Id)
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$botoesDir .= '<button id="sistemaEtiquetasQtdProduto_btn">Imprimir</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#sistemaEtiquetasQtdProduto_btn').unbind('click').bind('click', function() {
  					cad020ImpTag('{$r_D00X_Id}', '{$g['divId']}', $('#impEtiquetas').val(), $('#impQuantidade').val(), $('#tipoEtiqueta').val());
				});
			} </script></div>
EOT;
	break;
	
	case 'etiquetasGenericaXX000':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/sistema/etiquetas/tab/etiquetasGenericaXX000/', '&D00X_Id=' . @$r_D00X_Id)
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$botoesDir .= '<button id="etiquetasGenericaXX000_btn">Imprimir</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etiquetasGenericaXX000_btn').unbind('click').bind('click', function() {
  					etiquetasGenericaXX000('{$g['divId']}', $('#impLetras').val(), $('#impInicio').val(), $('#impFim').val(), $('#addZeros').val());
				});
			} </script></div>
EOT;
	break;
	
	case 'sistemaSelecionarModelo':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/etiquetas/tab/tabSistemaSelecionarModelo/', '&D00X_Id=' . $r_D00X_Id)
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = '';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		// $botoesDir .= '<button id="sistemaEtiquetasQtdProduto_btn">Imprimir</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#sistemaEtiquetasQtdProduto_btn').unbind('click').bind('click', function() {
  					cad020ImpTag('{$r_D00X_Id}', '{$g['divId']}', $('#impEtiquetas').val(), $('#impQuantidade').val(), $('#tipoEtiqueta').val());
				});
			} </script></div>
EOT;
	break;
}
?>

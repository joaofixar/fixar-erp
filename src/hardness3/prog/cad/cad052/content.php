<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'cad052ContentEngenharia':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad052/tab/cad052TabGeral/', '&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id)),
			//array($content1, '/cad/cad052/tab/cad052tabConsumiveis/', '&acaoId=' . urlencode($g['divId']) . '&D001_Id=' . urlencode($r_D001_Id) . '&D075_Id=' . urlencode($r_D075_Id)),
		);
		echo  gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		/*$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad002FormEcommerce_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');*/
	break;
	
	case 'cad052ContentConsumiveis':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad052/grid/cad052gridConsumiveis/', '&D001_Id=' . urlencode($r_D001_Id) . '&D075_Id=' . urlencode($r_D075_Id)),
			//array($content1, '/cad/cad052/tab/cad052tabConsumiveis/', '&acaoId=' . urlencode($g['divId']) . '&D001_Id=' . urlencode($r_D001_Id) . '&D075_Id=' . urlencode($r_D075_Id)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
		case 'cad052ContentConsumivel':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad052/form/cad052formConsumiveis/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id) . '&D075_Id=' . urlencode($r_D075_Id)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad052contentItensComposicao':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)

		require_once("bibliotecas/classes/CAD052.php");
		$CAD052 = new CAD052();

			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo  $g['smarty']->fetch('content-full.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/cad/cad052/form/cad052formItensComposicao/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode(@$r_D001_Id)),
			);
			echo  gProcessaAcoes($actions);
	break;
	
	case 'cad052contentProcessos':
		require_once("bibliotecas/classes/CAD052.php");
		$CAD052 = new CAD052();
		$retorno = $CAD052->engenhariaLiberadaProcessoIncluir($r_D001_Id);
		if($retorno === true){
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo  $g['smarty']->fetch('content-full.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/cad/cad052/form/cad052formProcessos/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id)),
			);
			echo  gProcessaAcoes($actions);
		}else{
			return $retorno;
		}
	break;
	
	case 'cad052ContentDemonstracao':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad052/outro/cad052Demonstracao/', '&D001_Id=' . urlencode($r_D001_Id)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad052ContentProduto':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad052/form/cad052formProduto/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
	break;
}
?>
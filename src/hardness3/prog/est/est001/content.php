<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since ...
	*/
	case 'est001ContentDivergenciasEstoque':
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
			array($content1, '/est/est001/tab/est001TabDivergenciasEstoque/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est001ContentDivergenciasEstoqueIncluir':
		//Verifica se a D.E já foi finalizada
		$sql=mysql_query("select T029_Id,T029_Flag_Ok from T029 where T029_Id='{$r_acaoId}'");
		$row=mysql_fetch_array($sql);
		
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
			array($content1, '/est/est001/tab/est001TabDivergenciasEstoqueIncluir/', '&acaoId=' . urlencode($r_acaoId). '&T029_Id=' . urlencode($r_acaoId) . '&T029_Flag_Ok=' . urlencode($row['T029_Flag_Ok']))
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		
		$botoesMid = $botoesEsq = $botoesDir = '';
		$disabled = '';
		if (!isset($g['c029Ids'][180])) {
			$disabled = ' disabled="disabled"';
		}
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();

	break;
	
	case 'est001ContentDivergenciasEstoqueIncluirProduto':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '100px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est001/form/est001FormDivergenciasEstoqueIncluirProduto/', '&tab=geral&T029_Id=' . urlencode($r_T029_Id) . '&divReload=' . urlencode($content2)),
			array($content2, '/est/est001/grid/est001GridDivergenciasEstoqueProdutos/', '&acaoId=' . urlencode($r_acaoId). '&T029_Id=' . urlencode($r_T029_Id) . '&divReload=' . urlencode($content1))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'est001ContentDivergenciasEstoqueIncluirProdutoGrid':
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
			array($content1, '/est/est001/grid/est001GridDivergenciasEstoqueProdutos/', '&acaoId=' . urlencode($r_acaoId). '&T029_Id=' . urlencode($r_T029_Id) . '&divReload=' . urlencode($content1))
		);
		echo gProcessaAcoes($actions);

	break;

}
?>
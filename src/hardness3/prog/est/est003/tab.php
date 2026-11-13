<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	/**
	* Chamado em: est003Content01
	*/

	case 'est003TabPrincipal':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/est/est003/grid/est003GridNaoLiberado/', 'Não Liberado', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		array(uniqid(), '/est/est003/grid/est003GridAguardandoProdCompra', 'Aguardando Prod/Compra', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		array(uniqid(), '/est/est003/grid/est003GridDisponivelParaFaturar/', 'Disponível p/ Faturar', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		array(uniqid(), '/est/est003/grid/est003GridDivergencias/', 'Divergências', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		array(uniqid(), '/est/est003/grid/est003GridListaBox/', 'Lista Box', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		array(uniqid(), '/est/est003/content/est003Content/', 'Estoque', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;

	case 'est003TabPedido':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad020/grid/cad020lista/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/cad/cad019/grid/cad019GridLocacao/', 'Locações', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/cad/cad051/grid/cad051GridPrincipal/', 'Etiquetas', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/est/est003/outros/est003OutrosEtiquetas/', 'Etiq. Especiais', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/est/est003/grid/est003GridCatalogo/', 'Catálogo', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/cad/cad002/grid/cad002GridValidade/', 'Validade'),
			//array(uniqid(), '/est/est003/grid/est003GridProdutosDefeitos/', 'Outros Estoques'),
			array(uniqid(), '/est/est003/grid/est003GridBalancoCiclico/', 'Balanço'),
			array(uniqid(), '/est/est003/content/est003ContentLoja/', 'Loja'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est003TabProdutos':

		// Definição das Tabs
		$tabs = array();
		$tabColetas = uniqid();
		$tabMontagens = uniqid();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad020/grid/cad020lista/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/cad/cad019/grid/cad019GridLocacao/', 'Locações', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/cad/cad051/grid/cad051GridPrincipal/', 'Etiquetas', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/est/est003/outros/est003OutrosEtiquetas/', 'Etiq. Especiais', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/est/est003/grid/est003GridCatalogo/', 'Catálogo', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/cad/cad002/grid/cad002GridValidade/', 'Validade'),
			array(uniqid(), '/est/est003/grid/est003GridProdutosDefeitos/', 'Outros Estoques'),
			array(uniqid(), '/est/est003/grid/est003GridBalancoCiclico/', 'Balanço'),
			array(uniqid(), '/est/est003/content/est003ContentLoja/', 'Loja'),
			array(uniqid(), '/est/est003/content/est003ContentMovimentacao/', 'Movimentação'),
			array($tabColetas, '/est/est003/content/est003ContentColetas/', 'Coletas'),
		);
		if(isset($g['c029Ids'][19])){
			$tabsList[] = array(uniqid(), '/est/est001/grid/est001GridDivergenciasEstoque/', 'D.E.');
		}
		if(isset($g['c029Ids'][24])){
			$tabsList[] = array($tabMontagens, '/est/est007/grid/est007GridMontagens/', 'Montagens/Desmontagens');
		}

		$tabsList[] = array(uniqid(), '/est/est003/content/est003ContentParticipante/', 'Participante');

		if($r_tabColetar === 'S'){	
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', '#{$tabColetas}');
					}, 500);
				} </script></div>
EOT;
		}
		if($r_tabMontagens === 'S'){	
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', '#{$tabMontagens}');
					}, 500);
				} </script></div>
EOT;
		}
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est003TabIncluirDE':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est003/form/est003FormIncluirDE/', 'Divergência de Estoque', "&tab=geral&T006_Id=" . urlencode($r_T006_Id))
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est003TabLoja':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est003/grid/est003GridPrecoLoja/', 'Preços para Alterar'),
			array(uniqid(), '/est/est003/grid/est003GridReabastecimentoLoja/', 'Reabastecimento'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est003TabPrecoLojaHistoricoItem':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est003/grid/est003GridPrecoLojaHistoricoItem/', 'Itens',  '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est003TabHistoricoLojaPreco':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est003/grid/est003GridPrecoLojaHistorico/', 'Histórico'),
			array(uniqid(), '/est/est003/grid/est003GridPrecoLojaHistoricoProdutos/', 'Produtos'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est003TabMovimentacao':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est003/grid/est003GridMovimentacao/', 'Movimentacão'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est003TabLocacoes':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/locacao/', 'Locações', "&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est003TabColetas':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est003/grid/est003GridColetar/', 'Coletar', "&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/est/est003/grid/est003GridEmColeta/', 'Em Coleta', "&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/est/est003/grid/est003GridColetado/', 'Coletado', "&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est003TabParticipante':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est003/grid/est003GridEstoqueDeTerceiros/', 'Estoque de Terceiros'),
			array(uniqid(), '/est/est003/grid/est003GridEstoqueEmTerceiros/', 'Estoque em Terceiros'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

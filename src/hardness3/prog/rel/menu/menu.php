<?php
namespace hardness;
include 'prog/sistema/funcoes/pesquisaGridCallback.php';

/**
* Grid de pesquisa para o campo do formulário tipo "pesquisaGrid"
*/
switch($g['r'][3]){
	case 'content':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Width', '150px');
		$g['smarty']->assign('tipo', '2colunas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/rel/menu/menu/mostraListaRelatorios/', '&dados=' . urlencode($r_dados) . '&href=' . urlencode($r_href) . '&divReload=' . urlencode($content2)),
			array($content2, '/rel/menu/menu/mostraGrid/', '')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'listaRelatorios':
		// Não exibir a coluna "+" ao final do grid
		$g['pularAdicionarColuna'] = true;

		//Layout do grid que lista os relatórios
		$grid = array();
		$grid[] = array('colunaValor' => 'relatorio', 'colunaTitulo' => '', 'chavePrimaria' => true, 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'grupo', 'colunaTitulo' => '', 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'titulo', 'colunaTitulo' => 'Relatórios');

		//Lista de relatórios
		$relatorios = array();
		$relatorios['Vendas']['Data']					= array('Faturamento por data', '/rel/ven/grid/faturamentoData/', '164');
		//$relatorios['Vendas']['Fat.Expedição']		= array('Faturamento por expedição', '/rel/ven/grid/faturamentoExpedicao/', '164');
		$relatorios['Vendas']['Perfil']					= array('Faturamento por perfil', '/rel/ven/grid/faturamentoPerfil/', '164');
		$relatorios['Vendas']['UF']						= array('Faturamento por UF', '/rel/ven/grid/faturamentoUf/', '164');
		$relatorios['Vendas']['Vendedor']				= array('Faturamento por vendedor', '/rel/ven/grid/faturamentoVendedor/', '164');
		$relatorios['Vendas']['Departamento']			= array('Faturamento por departamento', '/rel/ven/grid/faturamentoDepartamento/', '164');
		$relatorios['Vendas']['Resumo vendedor']		= array('Resumo vendedor', '/rel/ven/grid/resumoVendedor/', '177');
		$relatorios['Vendas']['PM - Prazo Médio']		= array('Analítico de vendas por prazo médio', '/rel/ven/tab/prazoMedio/', '195');
		$relatorios['Vendas']['Cliente R$']				= array('Analítico de vendas em reais', '/rel/ven/grid/analiticoVendasPeriodo/', '195');
		$relatorios['Vendas']['Cliente Qtde']			= array('Analítico de vendas por quantidade produtos', '/rel/ven/grid/analiticoVendasPeriodoQte/', '195');
		$relatorios['Vendas']['Produto R$']				= array('Analítico de vendas por quantidade produtos', '/rel/ven/grid/analiticoVendasPeriodoValorProduto/', '195');
		$relatorios['Vendas']['Produto Qtde']			= array('Analítico de vendas por quantidade produtos', '/rel/ven/grid/analiticoVendasPeriodoQteProduto/', '195');
		$relatorios['Compras']['Fornecedor R$']			= array('Analítico de compras em reais', '/rel/ven/grid/analiticoComprasPeriodo/', '226');
		$relatorios['Compras']['Fornecedor Qtde']		= array('Analítico de compras por quantidade produtos', '/rel/ven/grid/analiticoComprasPeriodoQte/', '226');
		$relatorios['Compras']['Produto R$']			= array('Analítico de compras por quantidade produtos', '/rel/ven/grid/analiticoComprasPeriodoValorProduto/', '226');
		$relatorios['Compras']['Produto Qtde']			= array('Analítico de compras por quantidade produtos', '/rel/ven/grid/analiticoComprasPeriodoQteProduto/', '226');
		$relatorios['Estoque']['Inventário']			= array('Inventário', '/rel/est/grid/inventario/', '153');
		$relatorios['Estoque']['Movimentação Sintético']= array('Movimentação Sintético', '/rel/est/grid/estoque/', '198');
		$relatorios['Financeiro']['DRE']				= array('DRE', '/rel/fin/tab/dre/', '198');
		$relatorios['Financeiro']['Fluxo de Caixa']		= array('Fluxo de Caixa', '/rel/fin/grid/fluxoCaixa/', '149');
		$relatorios['Financeiro']['Balancete']	 		= array('Balancete', '/rel/fin/grid/balancete/', '162');
		$relatorios['Financeiro']['Inadimplência'] 		= array('Inadimplência', '/rel/fin/grid/inadimplencia/', '162');

		//Monta lista de relatórios
		$geradoDados = array();
		$count = 0;
		foreach ($relatorios as $categoria => $modulo) {
			foreach ($modulo as $relatorio => $param) {
				if(isset($g['c029Ids'][$param[2]])){
					$geradoDados[] = array($param[0].'|'.base64_encode($param[1]), strtoupper($categoria), $relatorio);
					$count++;
				}
			}
		}

		$gridFiltro = array(
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                    array('title' => 'Grupo', 'campoAgrupar' => 'grupo', 'campoMostrar' => 'grupo', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
                ),
            ),
        );

		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
 		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var href = $(this).closest('tr').attr('id').replace(/relatorio-/, '').split('|');
				tituloRelatorio = href[0];
				href = window.atob(href[1]);

				// refresh
				var gridId = $('#{$r_divReload}').find('.ui-tabs-panel').attr('id');
				$('#{$r_divReload}').find('.ui-state-default').html('<a href="#">'+tituloRelatorio+'</a>');
				$('#' + gridId).data('href', href);
				$('#' + gridId).data('titleTab', tituloRelatorio);
				divRefresh(gridId);
			});

		} </script></div>
EOT;

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('removerScrollLoading', true);
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->assign('gridTable', '');
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'mostraGrid':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			//array(uniqid(), $r_href, 'Dados', '&dimensao=&generateAgrupamento=' . (!isset($r_dimensao) ? '' : urlencode($r_dimensao))),
			array(uniqid(), '/rel/menu/menu/grid/', '...', '')
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'mostraListaRelatorios':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			//array(uniqid(), $r_href, 'Dados', '&dimensao=&generateAgrupamento=' . (!isset($r_dimensao) ? '' : urlencode($r_dimensao))),
			array(uniqid(), '/rel/menu/menu/listaRelatorios/', 'Relatórios', '')
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

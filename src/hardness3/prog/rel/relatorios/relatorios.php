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
			array($content1, '/rel/relatorios/relatorios/mostraListaRelatorios/', '&dados=' . urlencode($r_dados) . '&href=' . urlencode($r_href) . '&divReload=' . urlencode($content2)),
			array($content2, '/rel/relatorios/relatorios/mostraGrid/', '')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'listaRelatorios':
		//Layout do grid que lista os relatórios
		$grid = array();
		$grid[] = array('colunaValor' => 'relatorio', 'colunaTitulo' => '', 'chavePrimaria' => true, 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'grupo', 'colunaTitulo' => '', 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'titulo', 'colunaTitulo' => 'Relatórios');

		//Lista de relatórios
		$relatorios = array();
		$relatorios['Financeiro']['DRE'] = "/rel/fin/grid/relatorio/";
		$relatorios['Vendas']['Fat. por Data'] = "/rel/ven/grid/faturamentoData/";
		$relatorios['Vendas']['Fat. por Expedição'] = "/rel/ven/grid/faturamentoExpedicao/";
		$relatorios['Vendas']['Fat. por Perfil'] = "/rel/ven/grid/faturamentoPerfil/";
		$relatorios['Vendas']['Fat. por UF'] = "/rel/ven/grid/faturamentoUf/";
		$relatorios['Vendas']['Fat. por Vendedor'] = "/rel/ven/grid/faturamentoVendedor/";
		$relatorios['Vendas']['Fat. por Departamento'] = "/rel/ven/grid/faturamentoDepartamento/";


		//Monta lista de relatórios
		$geradoDados = array();
		$count = 0;
		foreach ($relatorios as $categoria => $modulo) {
			//$geradoDados[] = array('ignorar', '<span style="font-weight: bold; font-size: 1.2em;">'.strtoupper($categoria).'</span>');
			foreach ($modulo as $relatorio => $param) {
				$geradoDados[] = array(
					$relatorio.'|'.base64_encode($param), strtoupper($categoria),
					(!$count) ? '<span class="agrupamentoSelecionado" style="font-weight: bold;">' . $relatorio . '</b>' : $relatorio
				);
				$count++;
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

				// modifica a fonte
				$('.agrupamentoSelecionado').parent().html($('.agrupamentoSelecionado').html());
				var html = $(this).closest('tr').find('td').html();
				$(this).closest('tr').find('td').html('');
				$('<span>')
					.addClass('agrupamentoSelecionado')
					.css({
						'font-weight': 'bold'
					})
					.html(html)
					.appendTo($(this).closest('tr').find('td'))
				// refresh
				var gridId = $('#{$r_divReload}').find('.ui-tabs-panel').attr('id');
				$('#{$r_divReload}').find('.ui-state-default').html('<a href="#">'+tituloRelatorio+'</a>');
				$('#' + gridId).data('href', href);
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
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->assign('gridTable', '');
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'mostraGrid':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			//array(uniqid(), $r_href, 'Dados', '&dimensao=&generateAgrupamento=' . (!isset($r_dimensao) ? '' : urlencode($r_dimensao))),
			array(uniqid(), '/rel/relatorios/relatorios/grid/', '...', '')
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
			array(uniqid(), '/rel/relatorios/relatorios/listaRelatorios/', 'Relatórios', '')
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

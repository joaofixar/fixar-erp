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
			array($content1, '/sistema/funcoes/agrupamento/listaDimensoes/', '&dados=' . urlencode($r_dados) . '&href=' . urlencode($r_href) . '&divReload=' . urlencode($content2)),
			array($content2, '/sistema/funcoes/agrupamento/mostraGrid/', '&dados=' . urlencode($r_dados) . '&href=' . urlencode($r_href) . '&dimensao=' . (!isset($r_dimensao) ? '' : urlencode($r_dimensao)))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'listaDimensoes':
		// Não exibir a coluna "+" ao final do grid
		$g['pularAdicionarColuna'] = true;

		$dados = json_decode(base64_decode($r_dados), true);
		
		$grid = array();
		$grid[] = array('colunaValor' => 'Key', 'colunaTitulo' => '', 'chavePrimaria' => true, 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'Dimensao', 'colunaTitulo' => 'Dimensões');

		/**
		* Geração: Monta o SQL e retorna os dados
		*/
		//list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 500);
		$geradoDados = array();
		$count = 0;
		foreach ($dados['dimensoes'] as $dim) {
			$geradoDados[] = array(
				base64_encode($dim[0]),
				(!$count) ? '<span class="agrupamentoSelecionado" style="font-weight: bold; font-size: 1.5em;">' . $dim[1] . '</b>' : $dim[1]
			);
			$count++;
		}
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/Key-/, '');
				id = window.atob(id);
				// modifica a fonte
				$('.agrupamentoSelecionado').parent().html($('.agrupamentoSelecionado').html());
				var html = $(this).closest('tr').find('td:eq(1)').html();
				$(this).closest('tr').find('td:eq(1)').html('');
				$('<span>')
					.addClass('agrupamentoSelecionado')
					.css({
						'font-weight': 'bold',
						'font-size': '1.5em'
					})
					.html(html)
					.appendTo($(this).closest('tr').find('td:eq(1)'))
				// refresh
				divSetarAcaoId('{$r_divReload}', id, 'dimensao');
				divRefresh('{$r_divReload}');
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
			array(uniqid(), '/sistema/funcoes/agrupamento/grid/', 'Métricas', '&dados=' . urlencode($r_dados) . '&href=' . urlencode($r_href) . '&dimensao=' . (!isset($r_dimensao) ? '' : urlencode($r_dimensao)))
		);		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'grid':
		// Não exibir a coluna "+" ao final do grid
		$g['pularAdicionarColuna'] = true;

		// não é verificado a necessidade de alias em todos os campos
        if (isset($r_pegarColunas)) {
			$r_pegarColunas = 'ignorar';
        }
        if (isset($r_pegarHTMLColunas)) {
			$r_pegarHTMLColunas = 'ignorar';
        }
        
		
		/**
		* Dados do grid
		*/
		$dados = json_decode(base64_decode($r_dados), true);
		if (empty($r_dimensao)) {
			$agrup = array_shift($dados['dimensoes']);
		} else {
			$agrup = $dados['dimensoes'][$r_dimensao];
		}
		
		// novo ID para personalização deste grid (transformar ele em um id único)
		if (strpos($r_href, 'generateHTML=true&grid=') !== false) {
			// Caso for um Agrupamento impresso a partir da impressão do grid principal
			preg_match('/generateHTML=true&grid=([^&]+)/', $r_href, $matches);
			$r_href = urldecode($matches[1]);
		}
		$agrupSelect = $agrup[0];
		$agrupGroupBy = $agrup[0];
		if (stripos($agrupGroupBy, ' as ') !== false) {
			$agrupGroupBy = preg_split('/ as /i', $agrupGroupBy);
			$agrupGroupBy = $agrupGroupBy[0];
		}
		$href = trim($r_href, '/');
		$href = explode('/', $href);
		$href = "{$href[0]}/{$href[1]}/{$href[2]}/{$href[3]}";
        $pesquisaGridId = md5($agrupGroupBy.$href . '/').'_'.md5('sistema/funcoes/agrupamento/grid/');
        $pesquisaGridIdOriginal = md5($agrupGroupBy.$href . '/');
        // Personalização do GRID: por padrão o ID do grid é a sua propria URL (md5), aqui setamos o 'pesquisaGridId' para utilizar um ID diferente
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('pesquisaGridId', '{$pesquisaGridId}');
			} </script></div>
EOT;
       	
       	/**
		* Definição do Grid (Tabela)
		*/ 
		$grid = array();
		$grid[] = array('colunaValor' => $agrupGroupBy, 'colunaTitulo' => $agrup[1]);
		if (stripos($agrup[0], 'Data')) {
			$grid[0]['callback'] = 'gCorrigeData';
		}
		foreach ($dados['metricas'] as $metrica) {
			if (!preg_match('/sum/i', $metrica[0])) {
				$metrica[0] = "SUM({$metrica[0]})";
			}
			$grid[] = array('colunaValor' => $metrica[0], 'colunaTitulo' => $metrica[1], 'style'=>'width:1%;', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right');
		}
		
		/**
		* Definição da SQL
		*/
		$from = $dados['from'];
		
		// Retira o order by do extra padrão do grid para evitar erros
		$extra = explode("ORDER BY",$dados['extra']);
		$extra = $extra[0];

		// Campos com 'AS' no Group by
		// Injeta o GROUP BY no EXTRA
		if (preg_match('/GROUP BY/i', $extra)) {
			$extra = preg_replace('/GROUP BY/i', 'GROUP BY', $extra);
			$extra = explode('GROUP BY', $extra);
			$extra = $extra[0] . " GROUP BY {$agrupGroupBy}";
		} else if (preg_match('/ORDER BY/i', $extra)) {
			$extra = preg_replace('/ORDER BY/i', 'ORDER BY', $extra);
			$extra = str_replace('ORDER BY', " GROUP BY {$agrupGroupBy} ORDER BY", $extra);
		} else {
			$extra .= " GROUP BY {$agrupGroupBy}";
		}
		
		/**
		* Filtro
		*/
		$gridFiltro = array();
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, $pesquisaGridId);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		if (!empty($gridFiltro)) {
			echo $g['ui']->gridFiltroPrint($gridFiltro, $pesquisaGridId);
		}

		// Sobrescreve o texto do filtro com o do grid original
		$r_titleTab = $r_tituloGrid . '||' . $agrup[1];
		$g['ui']->textoFiltroImprimir = $dados['textoFiltroImprimir'];

		// Injeta o WHERE do grid pai (sempre será utilizado o WHERE do GRID Pai
		$parteWhereExtraGridPai = gPegarWhereAgrupamento($dados['extra'], true);
		$extra1 = gInsertExtraWherePrincipal($extra, $parteWhereExtraGridPai);

		// Pega o filtro do Grid PAI (que gerou este agrupamento), e adiciona o que foi filtrado no global (para poder utilizar as variaveis do filtradas no colunaValor)
		$filtro = md5($href);
		if (strpos($r_href, 'etc015GerarGrid') !== false) {
			preg_match('/acaoId=([0-9])+/', $r_href, $matches);
			$sql = "SELECT * FROM D140 WHERE D140_Id = '{$matches[1]}';";
			$res = mysql_query($sql);
			$g['D140'] = $row = mysql_fetch_assoc($res);
			$g['D140']['D140-Filtro_Texto'] = '';
			$sql = $row['D140_SQL'];
			$filtro = 'D140_' . $row['D140_Nome_Md5'];

		}
		$file = $g['pathDadosUsuario'] . 'gridFiltro/' . $filtro;
		if (file_exists($file)) {
			$filtro = file_get_contents($file);
			$filtro = unserialize($filtro);
			foreach ($filtro['campos'] as $key => $value) {
				$$key = $value['busca'];
			}
		}
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000, $pesquisaGridId);
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados, $pesquisaGridId);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', $pesquisaGridId);
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId'], $pesquisaGridId), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); };
	break;
}
?>

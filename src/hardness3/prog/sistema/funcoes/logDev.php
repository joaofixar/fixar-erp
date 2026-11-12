<?php
namespace hardness;

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
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/logDev/tab/')
		);		
		echo gProcessaAcoes($actions);
	break;
	
	case 'tab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/sistema/funcoes/logDev/grid/', 'Log'),
		);		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'grid':
		/**
		 * Definição do Grid (Tabela)
		 */                           
		$grid = array(
			array('colunaValor' => 'T211_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
			array('colunaValor' => 'T211_Data_Hora ', 'colunaTitulo' => 'Data/Hora', 'callback' => 'gCorrigeData', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T211_C004_Id', 'colunaTitulo' => 'E', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T211_C007_Id ', 'colunaTitulo' => 'U', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T211_Grupo', 'colunaTitulo' => 'Grupo', 'style' => 'font-weight: bold; font-size: 1.1em; text-align: center; width: 1%;'),
			array('colunaValor' => 'T211_Flag_Nivel', 'colunaTitulo' => 'Nivel', 'style' => 'text-align: center; width: 1%;', 'callback' => 'T211FlagNivel'),
			array('colunaValor' => 'T211_Mensagem ', 'colunaTitulo' => 'Log'),
			array('colunaValor' => 'T211_Backtrace ', 'colunaTitulo' => 'Backtrace', 'callback' => 'backtrace', 'style' => 'width: 1%; text-align: right;'),
		);
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T211';

		$extra = <<<EOT
		WHERE
			T211_C004_Id = '{$g['empresaAtual']}'
			AND T211_C007_Id = '{$g['usuarioAtual']}'
		ORDER BY T211_Data_Hora DESC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T211_Flag_Nivel' => array('titulo' => 'Nivel', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'DEBUG', 'value' => '3'),
				array('title' => 'WARNING', 'value' => '2'),
				array('title' => 'FATAL', 'value' => '1'),
			), 'naoGerarExtra' => false),
			'T211_Grupo' => array('titulo' => 'Grupo', 'tipo' => 'text'),
			'T211_Mensagem' => array('titulo' => 'Mensagem', 'tipo' => 'texto'),
			'T211_Backtrace' => array('titulo' => 'Backtrace', 'tipo' => 'texto'),
			'T211_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'numero'),
			'T211_C007_Id' => array('titulo' => 'Usuário', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);


		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

}
?>
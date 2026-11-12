<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'contentErros':
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
			array($content1, '/sistema/logSistema/outro/tabErros/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'tabErros':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/sistema/logSistema/outro/gridErros/', 'Logs', '')
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'gridErros':
		global $_buscarBancoAuditoria;
		$_buscarBancoAuditoria=true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T245_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
			array('colunaValor' => "CONCAT(T245_Tabela,'_Id-',T245_Registro_Id)", 'colunaTitulo'=>'Chave Tabela', 'style' => 'width: 1%'),
			array('colunaValor' => 'T245_C004_Id', 'colunaTitulo'=>'C004', 'style' => 'width: 1%', 'align' => 'center'),
			array('colunaValor' => "T245_C007_Id as C007_Id", 'colunaTitulo' => 'Nome','callback' => 'retornaC007Nome'),
			array('colunaValor' => 'T245_DataHora', 'colunaTitulo' => 'Data Hora', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'T245_Log', 'colunaExibir'=>false),
			array('colunaValor' => 'T245_Flag_Erro', 'colunaExibir'=>false),
			array('colunaValor' => "'Log'", 'colunaTitulo' => 'Log', 'callback' => 'abrirJanela', 'callbackParameter' => array('Log', '/sistema/logSistema/outro/log/', 'T245_Id', '600,450')),
			array('colunaValor' => 'T245_Referencia', 'colunaTitulo' => 'Referência'),
			array('colunaValor' => 'T245_Descricao', 'colunaTitulo' => 'Descrição'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T245';

		$extra = <<<EOT
EOT;

		function gridLinha($array) {
			if($array['todosCampos']['T245_Flag_Erro']=='S'){
				return 'linhaVermelho';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T245_Tabela' => array('titulo' => 'Tabela', 'tipo' => 'texto'),
			'T245_Registro_Id' => array('titulo' => 'Registro Id', 'tipo' => 'texto'),
			'T245_DataHora' => array('titulo' => 'Data', 'tipo' => 'data'),
			'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T245_Referencia' => array('titulo' => 'Referência', 'tipo' => 'texto'),
			'T245_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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

    case 'log':
		$sql = mysqli_query("SELECT * FROM T245 WHERE T245_Id = '{$r_acaoId}'",$g['conexaoBancoAuditoria']);
		$row = mysql_fetch_array($sql);
		echo "<div style='font-size: 12px;'>".$row['T245_Log']."</div>";
	break;

	case 'contentGeral':
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
			array($content1, '/sistema/logSistema/outro/tabGeral/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'tabGeral':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/sistema/logSistema/outro/gridGeral/', 'Logs', '')
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'gridGeral':
		global $_buscarBancoAuditoria;
		$_buscarBancoAuditoria=true;
		
		/**
		* Definição do Grid (Tabela)
		*/
		
		$grid = array(
			array('colunaValor' => 'T265_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
			array('colunaValor' => 'T265_Data_Inicio_Log', 'colunaTitulo' => 'Início', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'T265_Data_Fim_Log', 'colunaTitulo' => 'Fim', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => "TIME_FORMAT(SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, T265_Data_Inicio_Log, T265_Data_Fim_Log))),'%H:%i:%s')", 'colunaTitulo' => 'Duração', 'style' => 'width: 1%'),
			array('colunaValor' => 'T265_Referencia', 'colunaTitulo' => 'Referência'),
			array('colunaValor' => 'T265_Descricao', 'colunaTitulo' => 'Descrição'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T265';

		$extra = <<<EOT
			GROUP BY T265_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T265_Data_Inicio_Log' => array('titulo' => 'Início', 'tipo' => 'data'),
			'T265_Data_Fim_Log' => array('titulo' => 'Fim', 'tipo' => 'data'),
			'T265_Referencia' => array('titulo' => 'Referência', 'tipo' => 'texto'),
			'T265_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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
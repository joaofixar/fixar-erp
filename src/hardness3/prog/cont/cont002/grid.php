<?php

namespace hardness;

require_once('prog/cont/cont002/grid_func-callback.php');

switch ($g['r'][3]) {
	case 'cont002GridLancamentos':
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T302_Id', $g['divId'])),
			array('colunaValor' => 'T302_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T302_Data', 'colunaTitulo' => 'Data Lançamento','colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T302_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D193_Nome_Conta', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'T302_Valor', 'colunaTitulo' => 'Valor','colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
			array('colunaValor' => 'T302_Credito_Debito', 'colunaTitulo' => 'Operação', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T302_Tabela', 'colunaTitulo' => 'Tabela Origem','colunaAlinhar' => 'center'),			
			array('colunaValor' => 'T302_Tabela_Id', "callback" => 'abrirDocumento','colunaTitulo' => 'Tabela Id','colunaAlinhar' => 'center'),					
			array('colunaValor' => 'T302_Entidade', 'colunaTitulo' => 'Entidade', 'colunaExibir' => false),				
			array('colunaValor' => 'D194_Link', 'colunaTitulo' => 'Link', 'colunaExibir' => false),					
		);

		/**
		 * Definição da SQL
		 */
		$from = "T302";
		$extra = <<<EOT
		LEFT JOIN D193 ON D193_Id = T302_D193_Id
		LEFT JOIN D195 ON D195_Id = T302_Entidade
		LEFT JOIN D194 ON D194_Id = D195_D194_Id
		WHERE T302_C004_Id = '{$g['empresaAtual']}'						
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cont002FormLancamentoBtnIncluir'),
			array('titulo' => 'Processar Lançamentos', 'id' => 'cont002ProcessaLancamentosBtn'),
		);
		
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cont002FormLancamentoBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Lançamento', '/cont/cont002/content/cont002ContentCadastroLancamento/', '&tab=geral', [630,290]);
				});
				$('#cont002ProcessaLancamentosBtn').unbind('click').bind('click', function(data) {					
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processar Lançamento', '/cont/cont002/content/cont002ContentProcessarLancamento/', '&tab=geral', [349,190]);
				});
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'T302_Credito_Debito' => array(
				'titulo' => 'Crédito/Débito', 'tipo' => 'select', 'select' => array(
					array('title' => '...', 'value' => ''),
					array('title' => 'Crédito', 'value' => 'C'),
					array('title' => 'Débito', 'value' => 'D')
				)
			),
			'T302_Data' => array('titulo' => 'Data', 'tipo' => 'data'),			
			'D193_Nome_Conta' => array('titulo' => 'Conta', 'tipo' => 'texto')
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T302_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');						
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Lançamento - ID: ' + acaoId, '/cont/cont002/content/cont002ContentCadastroLancamento/', '&acaoId=' + encodeURIComponent(acaoId), [630,290]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		
		
		/**
		 * Totais Almir
		 */
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);



		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));		
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) {
			ob_end_clean();
			ob_start();
			$g['smarty']->display('gridHTML.tpl');
		} else {
			$g['smarty']->display('grid.tpl');
		}
	break;

	case 'cont002GridEscrituracao':
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D194_Id', $g['divId'])),
			array('colunaValor' => 'D194_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D194_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D194_Tabela', 'colunaTitulo' => 'Tabela','colunaAlinhar' => 'center'),
			array('colunaValor' => 'D194_Campo_Data', 'colunaTitulo' => 'Campo Data'),			
		);

		/**
		 * Definição da SQL
		 */
		$from = "D194";
		$extra = <<<EOT

EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cont002FormEscrituracaoBtnIncluir'),			
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cont002FormEscrituracaoBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Escrituração', '/cont/cont002/content/cont002ContentCadastroEscrituracao/', '&tab=geral', [1070,540]);
				});				
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(		
			'D194_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto')
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D194_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');						
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar  Escrituração - ID: ' + acaoId, '/cont/cont002/content/cont002ContentCadastroEscrituracao/', '&acaoId=' + encodeURIComponent(acaoId) , [1070,615]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		/**
		 * Totais Almir
		 */
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);



		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) {
			ob_end_clean();
			ob_start();
			$g['smarty']->display('gridHTML.tpl');
		} else {
			$g['smarty']->display('grid.tpl');
		}
	break;
	case 'cont002GridCorrelacoes':
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D195_Id', $g['divId'])),
			array('colunaValor' => 'D195_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D195_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D195_Campo', 'colunaTitulo' => 'Campo','colunaAlinhar' => 'center'),	
			array('colunaValor' => 'D.D193_Nome_Conta', 'colunaTitulo' => 'Conta Débito','colunaAlinhar' => 'center'),	
			array('colunaValor' => 'C.D193_Nome_Conta', 'colunaTitulo' => 'Conta Crédito','colunaAlinhar' => 'center'),	
			array('colunaValor' => 'D195_Descricao_Contabil', 'colunaTitulo' => 'Descrição Contábil','colunaAlinhar' => 'center'),				
		);

		/**
		 * Definição da SQL
		 */
		$from = "D195";
		$extra = <<<EOT
		left join D194 ON D194_Id = D195_D194_Id
		left join D193 as D ON D.D193_Id = D195_Conta_Debito
		left join D193 as C ON C.D193_Id = D195_Conta_Credito
		where D195_D194_Id = '{$r_acaoId}'
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cont002FormCorrelacaoBtnIncluir'),			
		);
		echo Botoes::processar($botoes);		
		/**
		 * Botões (Versão Acima do GRID)
		 */

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cont002FormCorrelacaoBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Correlação', '/cont/cont002/content/cont002ContentCadastroCorrelacao/', '&tab=geral&D194_Id=' + encodeURIComponent('{$r_acaoId}'), [1070,540]);
				});				
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(		
			'D194_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto')
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D195_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');						
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Correlação - ID: ' + acaoId, '/cont/cont002/content/cont002ContentCadastroCorrelacao/', '&acaoId=' + encodeURIComponent(acaoId)  + '&D194_Id=' + encodeURIComponent('{$r_acaoId}'), [1070,540]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		/**
		 * Totais Almir
		 */
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);



		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) {
			ob_end_clean();
			ob_start();
			$g['smarty']->display('gridHTML.tpl');
		} else {
			$g['smarty']->display('grid.tpl');
		}
	break;
}

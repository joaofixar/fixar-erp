<?php

namespace hardness;

// require_once('prog/fis/fis001/grid_func-callback.php');

switch ($g['r'][3]) {
		/**
	 * Definição do Grid (Tabela)
	 */
	case 'fis013GridTipoProduto':
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D187_Id', $g['divId'])),
			array('colunaValor' => 'D187_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'D187_Codigo', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D187_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D187_Codigo_Plano_Conta_Contabil', 'colunaTitulo' => 'Código Plano De Contas Contábeis'),
		);

		/**
		 * Definição da SQL
		 */
		$from = "D187";
		$extra = <<<EOT
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fis013FormTipoProdutoBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fis013FormTipoProdutoBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Tipo Produto', '/fis/fis013/content/fis013ContentCadastroTipoProduto/', '&tab=geral', [500,250]);
				});
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D187_Codigo' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D187_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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
					var chavePrimaria = 'D187_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					console.log(acaoId);
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Tipo Produto - ID: ' + acaoId, '/fis/fis013/content/fis013ContentCadastroTipoProduto/', '&acaoId=' + encodeURIComponent(acaoId), [500,250]);
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

	case 'fis013GridObservacoes':
			$grid = array(
				array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D188_Id', $g['divId'])),
				array('colunaValor' => 'D188_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
				array('colunaValor' => 'D188_Codigo_Observacao', 'colunaTitulo' => 'Código'),
				array('colunaValor' => 'D188_Descricao_Observacao', 'colunaTitulo' => 'Descrição'),				
				array('colunaValor' => 'D189_Codigo_Ajuste', 'colunaTitulo' => 'Código Ajuste'),				
			);
	
			/**
			 * Definição da SQL
			 */
			$from = "D188";
			$extra = <<<EOT
			LEFT JOIN D189 ON D189_Id = D188_D189_Id
	EOT;
			$botoes = array(
				array('titulo' => 'Incluir', 'id' => 'fis013FormObservacoesBtnIncluir'),
			);
			echo Botoes::processar($botoes);
	
			/**
			 * Botões (Versão Acima do GRID)
			 */
	
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#fis013FormObservacoesBtnIncluir').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Observação Fiscal', '/fis/fis013/content/fis013ContentCadastroObservacaoFiscais/', '&tab=geral', [630,290]);
					});
				} </script></div>
	EOT;
	
			/**
			 * Filtro
			 */
			$gridFiltro = array(
				'D188_Codigo_Observacao' => array('titulo' => 'Código', 'tipo' => 'texto'),
				'D188_Descricao_Observacao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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
						var chavePrimaria = 'D188_Id';	
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').split('|');
						var acaoId = id[0].replace(chavePrimaria + '-', '');						
						// Ação: abrir nova janela
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Observação Fiscal - ID: ' + acaoId, '/fis/fis013/content/fis013ContentCadastroObservacaoFiscais/', '&acaoId=' + encodeURIComponent(acaoId), [630,290]);
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
	case 'fis013GridAjustes':
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D189_Id', $g['divId'])),
			array('colunaValor' => 'D189_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'D189_D018_Id', 'colunaTitulo' => 'D189_D018_Id','colunaExibir'=>false),			
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),			
			array('colunaValor' => 'D189_Codigo_Ajuste', 'colunaTitulo' => 'Código do Ajuste'),			
			array('colunaValor' => 'D189_Descricao_Ajuste', 'colunaTitulo' => 'Descrição'),				
		);

		/**
		 * Definição da SQL
		 */
		$from = "D189";
		$extra = <<<EOT
		LEFT JOIN D018 ON D018_Id = D189_D018_Id
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fis013FormAjustesBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fis013FormAjustesBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Ajuste', '/fis/fis013/content/fis013ContentCadastroAjuste/', '&tab=geral', [500,250]);
				});
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D189_Codigo_Ajuste' => array('titulo' => 'Código', 'tipo' => 'text'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'text'),			
			'D018_Estado' => array('titulo' => 'Estado', 'tipo' => 'text'),		
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
					var chavePrimaria = 'D189_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					console.log(acaoId);
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Ajuste - ID: ' + acaoId, '/fis/fis013/content/fis013ContentCadastroAjuste/', '&acaoId=' + encodeURIComponent(acaoId), [500,250]);
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

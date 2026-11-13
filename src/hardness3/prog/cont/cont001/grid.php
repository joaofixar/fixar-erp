<?php

namespace hardness;

switch ($g['r'][3]) {
	case 'cont001GridContas':
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D193_Id', $g['divId'])),
			array('colunaValor' => 'D193_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D193_Codigo', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D193_Nome_Conta', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'D193_Tipo', 'colunaTitulo' => 'Tipo','colunaAlinhar' => 'center'),
			array('colunaValor' => 'D193_Flag_Conta_Movimento', 'colunaTitulo' => 'Movimentação','colunaAlinhar' => 'center'),
		);

		/**
		 * Definição da SQL
		 */
		$from = "D193";
		$extra = <<<EOT
		order by D193_Codigo
EOT;
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cont001FormContaBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		 * Botões (Versão Acima do GRID)
		 */

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cont001FormContaBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Conta', '/cont/cont001/content/cont001ContentCadastroConta/', '&tab=geral', [630,290]);
				});
			} </script></div>
EOT;

		/**
		 * Filtro
		 */
		$gridFiltro = array(
			'D193_Codigo' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D193_Nome_Conta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
			'D193_Tipo' => array(
				'titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
					array('title' => '...', 'value' => ''),
					array('title' => 'Ativo', 'value' => 'A'),
					array('title' => 'Passivo', 'value' => 'P'),
					array('title' => 'Resultado', 'value' => 'R'),
				)
			),			
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
					var chavePrimaria = 'D193_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');						
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Conta - ID: ' + acaoId, '/cont/cont001/content/cont001ContentCadastroConta/', '&acaoId=' + encodeURIComponent(acaoId), [630,290]);
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

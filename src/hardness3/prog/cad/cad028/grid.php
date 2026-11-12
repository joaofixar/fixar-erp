<?php
namespace hardness;

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 22/11/2011
	*/                 
	case 'cad028GridSubGrupos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D002_Id', $g['divId'])),
			array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo', 'style' => 'width:1%'),
			array('colunaValor' => 'D002_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'style' => 'width:1%'),
			array('colunaValor' => 'D002_Descricao_Produto', 'colunaTitulo' => 'Sub-Grupo'),
		);

		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			array_push($grid, array('colunaValor' => 'D002A_Utiliza_Conversao',    'colunaTitulo' => 'Usa Conversão', 'style' => 'width:1%'));
			array_push($grid, array('colunaValor' => 'D002A_Tolerancia_Conversao', 'colunaTitulo' => '% Tolerância',  'style' => 'width:1%', 'callback' => 'gInserePorcentagem'));
		}

		/**
		* Definição da SQL
		*/
		$from = 'D002';
		$extra = <<<EOT
		   left join D003 on D003_Id=D002_D003_Id
		   left join D002a on D002_Id=D002a_D002_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad028FormCadastroSubGruposBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad028FormCadastroSubGruposBtnIncluir">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad028FormCadastroSubGruposBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro sub-grupo', '/cad/cad028/content/cad028ContentCadastroSubGrupos/', '&tab=geral', [600,250]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D002_Id' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D002_Descricao_Produto' => array('titulo' => 'Sub-Grupo', 'tipo' => 'texto'),
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
					var chavePrimaria = 'D002_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar sub-grupo - ID: ' + acaoId, '/cad/cad028/content/cad028ContentCadastroSubGrupos/', '&acaoId=' + encodeURIComponent(acaoId), [600,250]);
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

<?php
namespace hardness;

/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'cad053gridFuncoes':
		/**
		 * Definição do Grid (Tabela)
		 */		
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D078_Id', $g['divId'])),
			array('colunaValor' => 'D078_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D078_Nome', 'colunaTitulo'=>'Nome', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D078_Custo_Minuto', 'colunaTitulo'=>'Custo / Minuto', 'colunaAlinhar' => 'center'),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D078';
		$extra = <<<EOT
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Cadastrar Função', 'id' => 'inserirFuncao'),
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(

		);
		// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($gridFiltro);
			
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
		
		$novoIdTelaForm = uniqid();
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="inserirFuncao" style="margin-top: 5px;">Cadastrar Função</button>
				<div style="clear:both;"></div>*/
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
					$('#inserirFuncao').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Função', '/cad/cad053/content/cad053contentFuncao/', '', [360,85]);
					});
				} </script></div>
			</div>
EOT;
		
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D078_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Função - ID: ' + id, '/cad/cad053/content/cad053contentFuncao/', '&acaoId=' + encodeURIComponent(id), [360,85]);
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
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		break;	
			
	case 'cad053gridMaquinas':
		/**
		 * Definição do Grid (Tabela)
		 */		
		$grid = array(
	        array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D079_Id', $g['divId'])),
			array('colunaValor' => 'D079_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D079_Nome', 'colunaTitulo'=>'Nome', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D079_Custo_Minuto', 'colunaTitulo'=>'Custo / Minuto', 'colunaAlinhar' => 'center'),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D079';
		$extra = <<<EOT
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Cadastrar Máquina', 'id' => 'inserirMaquina'),
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(

		);
		// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
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
		$novoIdTelaForm = uniqid();
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="inserirMaquina" style="margin-top: 5px;">Cadastrar Máquina</button>
				<div style="clear:both;"></div>*/
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
					$('#inserirMaquina').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Máquina', '/cad/cad053/content/cad053contentMaquina/', '', [360,85]);
					});
				} </script></div>
			</div>
EOT;
		
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D079_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Máquina - ID: ' + id, '/cad/cad053/content/cad053contentMaquina/', '&acaoId=' + encodeURIComponent(id), [360,85]);
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
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		break;	
	}
?>

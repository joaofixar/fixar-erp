<?php
namespace hardness;

require_once('prog/cad/cad060/grid_func-callback.php');

switch($g['r'][3]) {
	case 'cad060GridMontagemIPV':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T249_Id', $g['divId']), 'style' => 'width: 1%'),
 			array('colunaValor' => 'T249_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
 			array('colunaValor' => 'T249_Descricao', 'colunaTitulo' => 'Descrição', 'colunaEditar' => true),
 			array('colunaValor' => 'T249_Aliquota', 'colunaTitulo' => 'Alíquota', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaTotal' => true, 'colunaEditar' => true),
 			array('colunaValor' => 'T249_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'callback' => 'mostraTipoVariavel', 'colunaEditarGeraSelect' => 
 				array(
 					array('title' => '...', 'value' => ''),
 					array('title' => 'FRETE', 'value' => '1'),
 					array('title' => 'COMISSÃO', 'value' => '2'),
 					array('title' => 'LUCRO', 'value' => '3'),
 					array('title' => 'TRIBUTOS', 'value' => '4'),
 					array('title' => 'TAXA PORTADOR', 'value' => '5'),
 				)
 			),
 			array('colunaValor' => 'T249_Grupo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'colunaEditarGeraSelect' => 
 				array(
 					array('title' => '...', 'value' => ''),
 					array('title' => 'ICF', 'value' => 'ICF'),
 					array('title' => 'IPV', 'value' => 'IPV'),
 				)
 			),
 			array('colunaValor' => 'T249_Nivel_Acesso', 'colunaTitulo' => 'Nível Acesso', 'callback' => 'nomeDoNivel', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'colunaEditarGeraSelect' => 
 				array(
 					array('title' => '...', 'value' => ''),
 					array('title' => 'Diretor', 'value' => '4'),
 					array('title' => 'Supervisor', 'value' => '2'),
 					array('title' => 'Vendedor', 'value' => '1'),
 				)
 			),
 			array('colunaValor' => 'T249_Flag_Campo_Calculado', 'colunaTitulo' => 'Campo Calculado', 'callback' => 'nomeCampoCalculado', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'colunaEditarGeraSelect' => 
 				array(
 					array('title' => '...', 'value' => ''),
 					array('title' => 'Sim', 'value' => 'S'),
 					array('title' => 'Não', 'value' => 'N'),
 				)
 			),
 			array('colunaValor' => 'T249_Flag_Altera_Preco', 'colunaTitulo' => 'Alterar Preço', 'callback' => 'nomeCampoAlteraPreco', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'colunaEditarGeraSelect' => 
 				array(
 					array('title' => '...', 'value' => ''),
 					array('title' => 'Sim', 'value' => 'S'),
 					array('title' => 'Não', 'value' => 'N'),
 				)
 			),
		);
		/**
		 * Definição da SQL
		 */
		$from = "T249";
		
		$extra = <<<EOT
			WHERE T249_C004_Id = '{$g['empresaAtual']}'
EOT;

		$extraICF = <<<EOT
			WHERE T249_C004_Id = '{$g['empresaAtual']}' and T249_Grupo='ICF'
EOT;

		$extraIPV = <<<EOT
			WHERE T249_C004_Id = '{$g['empresaAtual']}' and T249_Grupo='IPV'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad060Incluir'),
			);
		echo Botoes::processar($botoes);
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad060Incluir" style="float: left; margin: 5px 10px 0 0;">Incluir</button>
				<div style="clear:both;"></div>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad060Incluir').unbind('click').bind('click', function(data) {
			 		$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad060/form_func-ajax/cad060IncluirVariavelIPV/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
         * Totais Almir
         */
		//$aliquotaICF = 
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Aliquota_ICF', 'tipo' => 'formula', 'valor' => "'{T249_Grupo}'=='ICF' ? {T249_Aliquota} : 0", 'titulo' => 'ICF', 'exibir' => false),
			array('nome' => 'Valor_ICF', 'tipo'=>'totais', 'valor' => '(100/(100-{Aliquota_ICF}))', 'titulo' => 'ICF', 'callback' => 'gCorrigeNumero', 'callbackParameter' => 4),
			array('nome' => 'Aliquota_IPV', 'tipo' => 'formula', 'valor' => "'{T249_Grupo}'=='IPV' ? {T249_Aliquota} : 0", 'titulo' => 'IPV', 'exibir' => false),
			array('nome' => 'Valor_IPV', 'tipo'=>'totais', 'valor' => '(100/(100-{Aliquota_IPV}))', 'titulo' => 'IPV', 'callback' => 'gCorrigeNumero', 'callbackParameter' => 4),
		);
	
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

  	case 'cad060GridTipoAgenda':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
 			array('colunaValor' => 'D155_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
 			array('colunaValor' => 'D155_Tipo', 'colunaTitulo' => 'Tipo'),
 			array('colunaValor' => 'D155_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditar' => true, 'style' => 'width: 1%'),
		);
		/**
		 * Definição da SQL
		 */
		$from = "D155";
		
		$extra = <<<EOT
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad060IncluirTipoAgenda'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad060IncluirTipoAgenda">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad060IncluirTipoAgenda').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Tipo agenda', '/cad/cad060/content/cad060ContentIncluirTipoAgenda/', '', [400,150]);
				});
			} </script></div>
EOT;
		

		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D155_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
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
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<script type="text/javascript">
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'SPAN') { return true; } 
					if (data.target.nodeName == 'SPAN' && $(data.target).hasClass('ui-icon')) { return true; }
					var id = $(this).closest('tr').attr('id').replace('D155_Id-', '');
					$(this).closest('tr').find('span').css('font-weight', 'normal');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Tipo agenda', '/cad/cad060/content/cad060ContentIncluirTipoAgenda/', '&acaoId=' + encodeURIComponent(id), [400,150]);
				});
			</script>
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

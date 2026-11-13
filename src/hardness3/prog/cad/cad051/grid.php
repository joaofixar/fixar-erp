<?php
namespace hardness;
require_once('prog/cad/cad051/grid_func-callback.php');

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author Weiberlan
	* @since 30/11/2011
	*/                 
	case 'cad051GridPrincipal':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha','callbackParameter' => array('D138_Id', $g['divId'])),
			array('colunaValor' => 'D138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%;'),
			array('colunaValor' => 'campoImpTag', 'colunaTitulo' => '-', 'colunaAlinhar' => 'center', 'style' => 'width: 1%', 'callback' => 'campoImpTag', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'D138_Flag_Editor', 'colunaTitulo' => 'Editor', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D138_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'cad051CampoTipo', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D138_Linguagem', 'colunaTitulo' => 'Linguagem', 'style' => 'width: 1%;'),
			array('colunaValor' => 'D138_Modelo', 'colunaTitulo' => 'Modelo'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D138';
		$extra = <<<EOT
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad051CadastroModeloEtiqueta">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad051CadastroModeloEtiqueta').unbind('click').bind('click', function(data) {
					var buttons = {
						"Código": function() { 
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Modelo', '/cad/cad051/content/cad051ContentCadastroModelo/', '&tab=geral', [500,500]);
							$(this).dialog("destroy").remove(); 
						},
						"Editor": function() { 
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editor Modelos Genérica', '/cad/cad051/content/cad051ContentImpressaoGenerica/', '&tab=geral', [800,500]);
							$(this).dialog("destroy").remove(); 
						},
						"Fechar": function() { 
							$(this).dialog("destroy").remove(); 
						}
					};
    				dialogConfirm("Escolha uma opção", "Código - Escrever uma etiqueta na linguagem da impressora<br /><br />Editor - Crie um etiqueta genérica a partir de um formulário", buttons);
				});
			} </script></div>
EOT;

		
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D138_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Produto', 'value' => 'P'),
                array('title' => 'Locação', 'value' => 'L'),
                array('title' => 'Cliente', 'value' => 'C'),
                array('title' => 'Generica', 'value' => 'G'),
                array('title' => 'Pedido', 'value' => 'S')
            ), 'naoGerarExtra' => false),
			'D138_Linguagem' => array('titulo' => 'Linguagem', 'tipo' => 'select',
                'select' => array(
                            array('title' => 'Todos', 'value' => ''),
                            array('title' => 'PPLA (Argox)', 'value' => 'PPLA'),
                            array('title' => 'ZPL (Zebra)', 'value' => 'ZPL'),
                        ),'naoGerarExtra' => false),
			'D138_Modelo' => array('titulo' => 'Modelo', 'tipo' => 'texto'),
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
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Modelos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
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
					
					// D138_Id
					var chavePrimaria = 'D138_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					
					// FlagEditor
					var chavePrimaria = 'D138_Flag_Editor';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var flagEditor = id[1].replace(chavePrimaria + '-', '');
					
					if (flagEditor == 'S') {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editor Modelos Genérica - ID: ' + acaoId, '/cad/cad051/content/cad051ContentImpressaoGenerica/', '&acaoId=' + encodeURIComponent(acaoId), [800,500]);
					} else {
	        			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Modelo - ID: ' + acaoId, '/cad/cad051/content/cad051ContentCadastroModelo/', '&acaoId=' + encodeURIComponent(acaoId), [500,500]);
					}
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

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

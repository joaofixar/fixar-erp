<?php
namespace hardness;
  /**
 * Weiberlan
 * 07/12/2011
 */
//--Carrega funções callback
require_once('prog/cad/cad036/grid_func-callback.php');

switch ($g['r'][3]) {
	
	case 'cad036GridChecklistDeBens':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D120_Id', $g['divId'])),
			array('colunaValor' => 'D120_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D120_D128_Id', 'colunaTitulo' => 'Tipo', 'colunaExibir'=>false),
			array('colunaValor' => 'D128_Tipo', 'colunaTitulo' => 'Tipo'),
			array('colunaValor' => 'D120_D037_Id', 'colunaTitulo' => 'IdUnidade', 'colunaExibir' => false),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
			array('colunaValor' => 'D120_Descricao', 'colunaTitulo' => 'Descrição Português'),
			array('colunaValor' => 'D120_Descricao_Ingles', 'colunaTitulo' => 'Descrição Inglês'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D120';
		$extra = <<<EOT
		left join D037 on D037_Id=D120_D037_Id
		left join D128 on D128_Id=D120_D128_Id
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad036GridChecklistDeBensBtnIncluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad036GridChecklistDeBensBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Checklist', '/cad/cad036/content/cad036ContentCadastroChecklistDeBens/', '&tab=geral', [430,250]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D120_Descricao' => array('titulo' => 'Descrição Português', 'tipo' => 'texto'),
			'D120_Descricao_Ingles' => array('titulo' => 'Descrição Inglês', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D120_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Checklist - ID: ' + acaoId, '/cad/cad036/content/cad036ContentCadastroChecklistDeBens/', '&acaoId=' + encodeURIComponent(acaoId), [430,250]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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

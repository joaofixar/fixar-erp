<?php
namespace hardness;
/**
 * Tela:Cadastros-TabelaFornecedor
 * 
 * Autor: Andrey Sliwinski
 */
	
	switch($g['r'][3]){
	// GRID
	case 'lista':
		$grid = array(
			array('colunaValor' => 'D070_Flag_Ativo', 'colunaTitulo' => '', 'colunaTipo'=>'Livre'),
			array('colunaValor' => 'D070_Id', 'colunaTitulo' => 'Número'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Nome Empresa'),
			array('colunaValor' => 'D070_Observacao', 'colunaTitulo' => 'Obeservação'),
			array('colunaValor' => 'D070_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'D070_Desconto', 'colunaTitulo' => '', 'colunaTipo'=>''),
		);
		$from = "D070";
		
		$extra = <<<EOT
			left join D024 on D024_Id=D070_D024_Id 
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
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
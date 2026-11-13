<?php
namespace hardness;

/*
 * Grid Curva ABC Vendedores - Felipe
 */

$count = 0;
function geraCount($array){
	global $count;
	$count++;
	return $count;
}

/**
 * Ações
 */
	switch ($g['r'][3]) {
		case 'lista':
			
		 /**
		 * Definição do Grid (Tabela)
		 */			
		$grid = array(						
			array('colunaValor' => "",'colunaTipo'=>'livre', 'colunaTitulo' => '','callback' => 'geraCount'),
			array('colunaValor' => "Vendedor.C007_Primeiro_Nome Vendedor", 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => "T126_Curva_ABC", 'colunaTitulo' => 'ABC'),
			array('colunaValor' => "Vendedor.C007_Flag_Comercial C007_Flag_Comercial", 'colunaTitulo' => 'Sit'),						
			array('colunaValor' => "T126_Ano", 'colunaTitulo' => 'Ano'),			
			array('colunaValor' => "T126_Vendas_01", 'colunaTitulo' => 'Jan'),
			array('colunaValor' => "T126_Vendas_02", 'colunaTitulo' => 'Fev'),
			array('colunaValor' => "T126_Vendas_03", 'colunaTitulo' => 'Mar'),
			array('colunaValor' => "T126_Vendas_04", 'colunaTitulo' => 'Abr'),
			array('colunaValor' => "T126_Vendas_05", 'colunaTitulo' => 'Mai'),
			array('colunaValor' => "T126_Vendas_06", 'colunaTitulo' => 'Jun'),
			array('colunaValor' => "T126_Vendas_07", 'colunaTitulo' => 'Jul'),
			array('colunaValor' => "T126_Vendas_08", 'colunaTitulo' => 'Ago'),
			array('colunaValor' => "T126_Vendas_09", 'colunaTitulo' => 'Set'),
			array('colunaValor' => "T126_Vendas_10", 'colunaTitulo' => 'Out'),
			array('colunaValor' => "T126_Vendas_11", 'colunaTitulo' => 'Nov'),
			array('colunaValor' => "T126_Vendas_12", 'colunaTitulo' => 'Dez'),
			array('colunaValor' => "T126_Vendas_Total", 'colunaTitulo' => 'Total'),
			array('colunaValor' => "Supervisor.C007_Primeiro_Nome Supervisor", 'colunaTitulo' => 'Supervisor'),			
		);
			
		/**
		* Definição da SQL
		*/
		$from = 'C007 as Vendedor';
			
		$extra = <<<EOT
		left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		left join T126 on T126_C007_Id=Vendedor.C007_Id
		limit 500
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
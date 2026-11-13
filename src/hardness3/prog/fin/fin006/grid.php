<?php
namespace hardness;
// Classes
//require_once('bibliotecas/classes/FIN006.php');
//$FIN006 = new FIN006();
// Carrega funções callback
require_once('prog/fin/fin006/grid_func-callback.php');
// Carrega funções do ajax
//require_once('prog/fin/fin006/grid_func-js.php');

/**
 * Ações
 */

switch($g['r'][3]){
	/**
	* Grid Principal
	* Chamado em: fin006tab01
	*/
	case 'fin006grid01':
		/**
		* Definição do Grid (Tabela)
		*/			           
    		
		$grid = array(				
				array('colunaValor'=>'T137_Ano', 'colunaTitulo'=>'Ano'),
				array('colunaValor'=>'T137_Mes', 'colunaTitulo'=>'Mês'),
				array('colunaValor'=>'T137_Data_Inicial', 'colunaTitulo'=>'Inicial','callback' => 'gCorrigeData'),
				array('colunaValor'=>'T137_Data_Final', 'colunaTitulo'=>'Final', 'callback' => 'gCorrigeData'),
				array('colunaValor'=>'VENDEDOR.C007_Id Vendedor_Id', 'colunaTitulo'=>'ID'),
				array('colunaValor'=>'VENDEDOR.C007_Flag_Vendedor Flag_Vendedor', 'colunaTitulo'=>'TP'),
				array('colunaValor'=>'VENDEDOR.C007_Primeiro_Nome Vendedor', 'colunaTitulo'=>'Vendedor'),
				array('colunaValor'=>'SUPERVISOR.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Supervisor'),
				array('colunaValor'=>'T137_Valor_CMV', 'colunaTitulo'=>'CMV Vendedor'),
				array('colunaValor'=>'T137_Valor_Vendas', 'colunaTitulo'=>'Vendas Vendedor'),
				array('colunaValor'=>'T137_Valor_Devolucoes', 'colunaTitulo'=>'Devoluções Vendedor'),
				array('colunaValor'=>'T137_IPV', 'colunaTitulo'=>'IPV Vendedor'),
				array('colunaValor'=>'T137_Valor_CMV_Equipe', 'colunaTitulo'=>'CMV Supervisor'),
				array('colunaValor'=>'T137_Valor_Vendas_Equipe', 'colunaTitulo'=>'Vendas Supervisor'),
				array('colunaValor'=>'T137_Valor_Devolucoes_Equipe', 'colunaTitulo'=>'Devoluções Supervisor'),
				array('colunaValor'=>'T137_IPV_Equipe', 'colunaTitulo'=>'IPV Supervisor'),
				array('colunaValor'=>'T137_Percentual_Aproveitamento', 'colunaTitulo'=>'Aproveitamento','callback' => 'gInserePorcentagem'),
				array('colunaValor'=>'T137_Valor_Meta', 'colunaTitulo'=>'Meta'),
				array('colunaValor'=>'T137_Percentual_Fechamento', 'colunaTitulo'=>'Fechamento','callback' => 'gInserePorcentagem'),
				array('colunaValor'=>'T137_Valor_Comissao', 'colunaTitulo'=>'Comissão'),
				array('colunaValor'=>'T137_Id', 'colunaExibir'=>false,'chavePrimaria' => true),
		);
		
		/**
		* Definição da SQL
		*/		
		$from = "T137";
		
		$extra = <<<EOT
					left join C007 as VENDEDOR on VENDEDOR.C007_Id=T137_C007_Id_Vendedor
    				left join C007 as SUPERVISOR on SUPERVISOR.C007_Id=T137_C007_Id_Supervisor
    				where T137_C004_Id={$g['empresaAtual']}
    				group by T137_Id
EOT;
		if (!empty($xConsulta_Clausula_Filtro)) {
			$extra .= <<<EOT
				      and $xConsulta_Clausula_Filtro
EOT;
		}
		
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T137_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Meta Comissões - ID: ' + id, '/fin/fin006/content/fin006content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [440,350]);
			});
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
		 
		 // calcular os totais
		 $total=array();
		 for ($i=0;$i<12;$i++){
		 	$total[$i]=0;
		 }
		 foreach ($dados as $linha){		 	
		 	$total[1]+=$linha[8];
		 	$total[2]+=$linha[9];
		 	$total[3]+=$linha[10];		 	
		 	$total[5]+=$linha[12];
		 	$total[6]+=$linha[13];
		 	$total[7]+=$linha[14];
		 	$total[9]+=$linha[17];
		 	$total[10]+=$linha[19];		 	
		 }
		 
		 // calcular o IPV vendedor
		 $total[4]=number_format($total[2]/$total[1],4,',','.');
		 
		 // calcular o IPV supervisor		 
		 $total[8]=number_format($total[6]/$total[5],4,',','.');
		 
		 // inserir o total na última linha da tabela
		 array_push($gridTabela['tbody'], array('dados'=>array('','','','','','','','<b>Total</b>','<b>'.number_format($total[1],2,',','.').'</b>','<b>'.number_format($total[2],2,',','.').'</b>','<b>'.number_format($total[3],2,',','.').'</b>','<b>'.$total[4].'</b>','<b>'.number_format($total[5],2,',','.').'</b>','<b>'.number_format($total[6],2,',','.').'</b>','<b>'.number_format($total[7],2,',','.').'</b>','<b>'.$total[8].'</b>','','<b>'.number_format($total[9],2,',','.').'</b>','','<b>'.number_format($total[10],2,',','.').'</b>')));
		 		 
         $g['smarty']->assign('thead', $gridTabela['thead']);
         $g['smarty']->assign('tbody', $gridTabela['tbody']);
         $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
         gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
         
    	/**
		* Botões
		*/
         // definição dos botões
        $botoesEsq = '<button type="button" id="fin006Totalizar">Totalizar</button>';
        $botoesDir = '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';        
		$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);		
        $g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl'); 

		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#fin006Totalizar').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Totalizar Comissões', '/fin/fin006/content/fin006content03/', '', [220,200]);
			});		
		} </script></div>
EOT;
		
	break;
}
?>
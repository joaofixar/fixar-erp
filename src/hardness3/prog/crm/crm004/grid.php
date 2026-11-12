<?php
namespace hardness;
/**
* Fábio
* 13/06/2012
*/

//Carrega funções callback
	require_once('prog/crm/crm004/grid_func-callback.php');




switch($g['r'][3]){
  	case 'crm004GridResumo':
		echo <<<EOT
			<div class="radioResumoUI" style="padding:5px;">
				<input type="radio" id="radioResumo1" class="noStyle" checked="checked" name="radio" /><label for="radioResumo1">Diario</label>
				<input type="radio" id="radioResumo2" class="noStyle" name="radio" /><label for="radioResumo2">Mensal</label>
				<input type="radio" id="radioResumo3" class="noStyle" name="radio" /><label for="radioResumo3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioResumoUI" ).buttonset();
				});
				$('#radioResumo1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm004/grid/crm004GridResumoMensal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm004/grid/crm004GridResumoAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "crm004_Dia_Semana", 'colunaTitulo' => 'Dia', 'callback' => "crm004_Dia_Semana", 'colunaTipo'=>'livre', 'style' => 'width:1%'),
			array('colunaValor' => "R001_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Faturados_Nr", 'colunaTitulo' => 'Faturados', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R001_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R001_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			WHERE 
				R001_D024_Id={$r_D024_Id} 
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0
			 	AND R001_D049_Id = 0 
			 	AND R001_D006_Id = 0
			ORDER BY 
				R001_Data
EOT;

		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['crm004_Dia_Semana']=='Domingo'){
				return 'linhaAzul';
			}
		}
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);		
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		 /**
		 * Totalizadores
		 */
		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<input type="text" class="whiteBg" id="" size="30%" value="" readonly></span>
				<input type="text" class="whiteBg" id="" size="11" value="" readonly></span>
				 <div style="clear:both;"></div>
			</div>
EOT;*/
 		/**
		* Botões (Versão Acima do GRID)
		*/
  
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

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
		$primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;	
	case 'crm004GridResumoMensal':
		echo <<<EOT
			<div class="radioResumoUI" style="padding:5px;">
				<input type="radio" id="radioResumo1" class="noStyle" name="radio" /><label for="radioResumo1">Diario</label>
				<input type="radio" id="radioResumo2" class="noStyle" checked="checked" name="radio" /><label for="radioResumo2">Mensal</label>
				<input type="radio" id="radioResumo3" class="noStyle" name="radio" /><label for="radioResumo3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioResumoUI" ).buttonset();
				});
				$('#radioResumo1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm004/grid/crm004GridResumo/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm004/grid/crm004GridResumoAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'callback'=>"retornaMes", 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R001_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "SUM(R001_Orcamentos_Nr) as R001_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Clientes) as R001_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Itens) as R001_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Valor_Total) as R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Pedidos_Nr) as R001_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Clientes) as R001_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Itens) as R001_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Valor_Total) as R001_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_Nr) as R001_Faturados_Nr", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Faturados_Clientes) as R001_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Faturados_Itens) as R001_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Faturados_Valor_Total) as R001_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_IPV) as R001_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(R001_Faturados_IPV_Real) as R001_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(R001_Devolucoes_Nr) as R001_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Devolucoes_Clientes) as R001_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Devolucoes_Valor_Total) as R001_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Compras_Nr) as R001_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Compras_Clientes) as R001_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Compras_Valor_Total) as R001_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			WHERE 
				R001_D024_Id={$r_D024_Id} 
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0
			 	AND R001_D049_Id = 0 
			 	AND R001_D006_Id = 0 
			GROUP BY 
				Month(R001_Data)
			ORDER BY 
				R001_Data
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'R001_C007_Id_Supervisor' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'R001_C007_Id_Vendedor' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		

		/**
		* Botões (Versão Acima do GRID)
		*/

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);		
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		 /**
		 * Totalizadores
		 */
 		/**
		* Botões (Versão Acima do GRID)
		*/
  
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			
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
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		$primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="2" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;	
		case 'crm004GridResumoAnual':
		echo <<<EOT
			<div class="radioResumoUI" style="padding:5px;">
				<input type="radio" id="radioResumo1" class="noStyle" name="radio" /><label for="radioResumo1">Diario</label>
				<input type="radio" id="radioResumo2" class="noStyle" name="radio" /><label for="radioResumo2">Mensal</label>
				<input type="radio" id="radioResumo3" class="noStyle" checked="checked" name="radio" /><label for="radioResumo3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioResumoUI" ).buttonset();
				});
				$('#radioResumo1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm004/grid/crm004GridResumo/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm004/grid/crm004GridResumoMensal/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback'=>"retornaAno"),
			array('colunaValor' => "R001_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "SUM(R001_Orcamentos_Nr) as R001_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Clientes) as R001_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Itens) as R001_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Valor_Total) as R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Pedidos_Nr) as R001_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Clientes) as R001_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Itens) as R001_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Valor_Total) as R001_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_Nr) as R001_Faturados_Nr", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Faturados_Clientes) as R001_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Faturados_Itens) as R001_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Faturados_Valor_Total) as R001_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_IPV) as R001_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(R001_Faturados_IPV_Real) as R001_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(R001_Devolucoes_Nr) as R001_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Devolucoes_Clientes) as R001_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Devolucoes_Valor_Total) as R001_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Compras_Nr) as R001_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Compras_Clientes) as R001_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Compras_Valor_Total) as R001_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			WHERE
				R001_D024_Id={$r_D024_Id} 
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0
			 	AND R001_D049_Id = 0 
			 	AND R001_D006_Id = 0 
			GROUP BY 
				Year(R001_Data)
			ORDER BY 
				R001_Data
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'R001_C007_Id_Supervisor' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'R001_C007_Id_Vendedor' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		

		/**
		* Botões (Versão Acima do GRID)
		*/

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);		
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		 /**
		 * Totalizadores
		 */
 		/**
		* Botões (Versão Acima do GRID)
		*/
  
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			
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
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		$primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="2" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;
}
?>

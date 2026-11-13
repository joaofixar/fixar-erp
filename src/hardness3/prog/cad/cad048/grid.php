<?php
namespace hardness;
/**
* Weiberlan
* 28/11/2011
*/
/** Classes
require_once('bibliotecas/classes/CAD001.php');
$CAD001 = new CAD001();
*/

/**
--Carrega funções callback
	require_once('prog/cad/cad048/grid_func-callback.php');

--Carrega funções do ajax
	require_once('prog/cad/cad048/grid_func-js.php');
*/


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'cad048GridModelosVeiculo':
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D133_Id', $g['divId'])),
 			array('colunaValor' => 'D133_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D133_Modelo', 'colunaTitulo' => 'Modelo'),
			
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "D133";
		
		$extra = <<<EOT
EOT;

			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad048Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad048Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Modelo de Veículo', '/cad/cad048/content/cad048contentCadastroModelo/','', [400,150]);
				});
			} </script></div>
EOT;
			
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D133_Modelo' => array('titulo' => 'Modelo', 'tipo' => 'texto'),
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
			/**
			* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Países<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
*/
			
			

			/**
			 * Define ação do click de abertura (ao clicar em uma linha do grid)
			 */
			$novoIdTelaForm = uniqid();
			echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D133_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Modelo - ID: ' + id, '/cad/cad048/content/cad048contentCadastroModelo/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [400,150]);
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

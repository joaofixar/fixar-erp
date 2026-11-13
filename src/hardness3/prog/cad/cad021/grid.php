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
	require_once('prog/cad/cad021/grid_func-callback.php');

--Carrega funções do ajax
	require_once('prog/cad/cad021/grid_func-js.php');
*/


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'cad021GridPortadoresPagar':
		$grid = array(
 			array('colunaValor' => 'D031_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false ),
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D031_Id', $g['divId'])),
			array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador' ),
     );
		
		/**
		 * Definição da SQL
		 */
		$from = "D031";
		
		$extra = <<<EOT
EOT;
	 	/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad021Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad021Incluir">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad021Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Portador', '/cad/cad021/content/cad021contentCadastroPortador/','', [350,250]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D031_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Portador - ID: ' + id, '/cad/cad021/content/cad021contentCadastroPortador/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [300,210]);
			});
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

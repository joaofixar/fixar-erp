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


require_once('prog/srv/srv004/grid_func-callback.php');
require_once('prog/srv/srv004/grid_func-js.php');



switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'srv004GridInspecao':

		$grid = array( 						
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T138_Data_Inspecao','colunaExibir' => false),
			array('colunaValor' => 'T138_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'callback' => 'retornaOperacaoTroca'),
			array('colunaValor' => 'T138_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),		
			array('colunaValor' => 'campoInspecao', 'colunaTitulo' => 'Aprovar','colunaTipo'=>'livre', 'callback'=>'campoMarcarInspecionado', 'style' => 'text-align:center'),	
			);
		
		/**
		 * Definição da SQL
		 */
		$from = "T138";
		
		$extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T138_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
			WHERE T138_T005_Id>0
			AND T138_Data_Inspecao IS NULL
EOT;

			/**
			* Botões (Versão Acima do GRID)
			*/
		
		/*
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad049Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad049Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Cor de Veículo', '/cad/cad049/content/cad049contentCadastroCor/','', [400,150]);
				});
			} </script></div>
EOT;
*/
			
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D001_Descricao_Produto,D082_Marca' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'T138_Operacao' => array('titulo' => 'Operação (T/P)', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
		
echo <<<EOT
   <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
         $('#{$g['divId']} .grid').css('width', '1%');
    } </script></div>
EOT;
			
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
			/*
			$novoIdTelaForm = uniqid();
			echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D134_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cor - ID: ' + id, '/cad/cad049/content/cad049contentCadastroCor/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [400,150]);
			});
		} </script></div>
EOT;
		*/
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

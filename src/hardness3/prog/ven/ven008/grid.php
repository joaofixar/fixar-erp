<?php
namespace hardness;
/**
 * Testes - Weiber - Programa Teste Grid Ordem de Serviço.
 *
 * select pego de: hardness\os\cabecalhos.php
 */

/**
 * Area para funções locais
 */
// Formata data para exibição

/**
 * Ações
 */
switch($g['r'][3]){
	// PADRÃO
	case 'divPadrao':
		$g['conteudo']['jsTexto'] = <<<EOT
$(document).ready(function() {
	// Carrega Grid
	$.ajax({
		url: '/ven/ven008/grid/lista/',
		dataType: 'html',
		data: 'ajax=true',
		success: function(data) {
			$('#divPadrao').html(data);
			return false;
		}
	});
});
EOT;
		break;

		// GRID

	case 'lista':

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => 'T122_Id', 'colunaTitulo' => 'Número'),
		array('colunaValor' => 'T122_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
		array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T122';

		$extra = <<<EOT

				left join C007 on T122_C007_Id=C007_Id
				left join D024 on T122_D024_Id=D024_Id
				order by 
				T122_Id desc
				
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
